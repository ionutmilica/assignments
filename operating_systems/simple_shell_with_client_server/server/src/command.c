#include "command.h"
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include <stdio.h>
#include <signal.h>
#include <sys/stat.h> 
#include "utils.h"

List* registered_cmds;

/**
 * Register command
 * 
 * @param name
 * @param callback
 */
void register_command(char* name, cmd_fct callback)
{
    if (registered_cmds == NULL) {
        registered_cmds = new_list();
    }
    
    cmd_callback* cmd = (cmd_callback*) malloc(sizeof(cmd_callback));
    cmd->name = strdup(name);
    cmd->callback = callback;
    
    list_add(registered_cmds, (void *) cmd);
}

/**
 * Check if a given name it's a registered command
 * 
 * @param name
 * @return 
 */
cmd_callback* has_command(char * name) {
    node_t tmp = registered_cmds->first;
    while (tmp) {
        cmd_callback* cmd = tmp->data;
        if (strcmp(cmd->name, name) == 0) {
            return cmd;
        }
        tmp = tmp->next;
    }
    
    return NULL;
}

/**
 * Allocate memory for a default command
 * 
 * @return command*
 */
command* new_command() 
{
    command* cmd = (command*) malloc(sizeof(command));
    cmd->ret = -1;
    cmd->flag = FLAG_NONE;
    cmd->type = CMD_DEFAULT;
    cmd->in = STDIN_FILENO;
    cmd->out = STDOUT_FILENO;
    
    return cmd;
}

/**
 * Free the memory of a command
 * 
 * @param cmd
 */
void free_command(command* cmd) 
{
    if (cmd != NULL) {
        if (cmd->name) {
            free(cmd->name);
        }
        list_free(cmd->args);
        free(cmd);
    }
}

/**
 * Converts the list element to command
 * 
 * @param cmd_ptr
 * @return 
 */
command* to_cmd(node_t cmd_ptr)
{
    return (command*) cmd_ptr->data;
}

/**
 * Check if a command exists
 * 
 * @param cmd
 * @return 
 */
int command_exists(command* cmd) {
    char* env = strdup(getenv("PATH"));
    char* path = strtok(env, ":");
    struct stat sb;
    if (cmd->cmd != NULL) {
        return 1;
    }
    while (path) {
        char* cmd_path = concat_paths(path, cmd->name);
        if ((stat(cmd_path, &sb) == 0) && (sb.st_mode & S_IXOTH)) {
            return 1;
        }
        path = strtok(NULL, ":");
    }
    return 0;
}

/**
 * Takes a linked list and returns argv & argc for the command
 * 
 * @param list
 * @param n
 * @return 
 */
char** prepare_args(List* list, int* n)
{
   int argc = 0;
   char ** args;
   
   args = malloc((list->counter + 1) * sizeof(char*));
   
   node_t c = list->first;
   
   while (c != NULL) {
       char* arg = (char*) c->data;
       args[argc++] = strdup(arg);
       c = c->next;
   }
   
   args[argc] = NULL;
   *n = argc;

   return args;
}


/**
 * Run a given command
 * 
 * @param cmd
 * @return 
 */
int run_cmd(command* cmd)
{
    pid_t ret = -1;
    char ** args;
    int argc = 0;

    args = prepare_args(cmd->args, &argc);

    if (cmd->cmd != NULL) {
        cmd->ret = ret = cmd->cmd->callback(argc, args, cmd->in, cmd->out);
        cmd->ret = ret; 
    } else {
        ret = fork();
        if (ret == 0) {
            dup2(cmd->in, STDIN_FILENO);
            dup2(cmd->out, STDOUT_FILENO);
            execvp(cmd->name, args);
        } else {
            wait(NULL);
        }
    }
    return ret;
}

/**
 * Run a command line. It will pipe/redirect every cmd that was found.
 * 
 * @param line
 */
void run_line(List* line)
{
    node_t cmd_ptr = line->first;

    int current_in = dup(STDIN_FILENO);
    
    while (cmd_ptr != NULL) {
        command* cmd = (command*) cmd_ptr->data;
        if (cmd->type != CMD_STRING && ! command_exists(cmd)) {
            printf("%s: command not found !\n", cmd->name);
            return;
        }
        
        switch (cmd->flag) {
            case FLAG_PIPE: {
                int r, fd[2];
               
                while(cmd_ptr != NULL) {
                    command* curr_cmd = (command*) cmd_ptr->data;
                    if (curr_cmd->type != CMD_STRING && ! command_exists(curr_cmd)) {
                        printf("%s: command not found !\n", curr_cmd->name);
                        return;
                    }
                    
                    pipe(fd);
                    r = fork();

                    if (r == 0) {
                        close(fd[0]);
                        curr_cmd->in = current_in;
                        if (cmd_ptr->next != NULL) {
                            if (curr_cmd->flag == FLAG_REDIRECT) { /** Redirect the last pipe to file **/
                                int file = open(to_cmd(cmd_ptr->next)->name, O_WRONLY | O_TRUNC | O_CREAT, S_IRUSR);

                                if (file < 0) {
                                    printf("Error occurred while writing to file via redirect !\n");
                                    return;
                                }
                                curr_cmd->out = file;
                            } else { /** Write to the next piped command **/
                                curr_cmd->out = fd[1];
                            }
                        }
                        run_cmd(curr_cmd);

                        close(fd[1]);
                        exit(0);
                    } else {
                        wait(NULL);
                        close(fd[1]);
                        current_in = fd[0];
                    }
                    
                    cmd_ptr = cmd_ptr->next;
                }
                
                if (cmd_ptr == NULL) {
                    return;
                }
            }
            break;
            case FLAG_REDIRECT: {
                node_t next_cmd_ptr = cmd_ptr->next;
                command* next_cmd = (command*) next_cmd_ptr->data;
                    
                if (next_cmd_ptr != NULL) {
                    int file = open(next_cmd->name, O_WRONLY | O_TRUNC | O_CREAT, S_IRUSR);
                    
                    if (file < 0) {
                        printf("Error occurred while writing to file via redirect !\n");
                        return;
                    }
                    cmd->out = file;
                    run_cmd(cmd);
                    close(file);
                }
            }
            break;
            default: {
                run_cmd(cmd);
                break;
            }
        }
        
        cmd_ptr = cmd_ptr->next;
    }
}
