#include "parser.h"
#include <string.h>
#include "command.h"
#include <stdio.h>
#include <stdlib.h>

/** Private functions **/

FLAG get_operator_flag(char op) 
{
    switch (op) {
        case '|':
            return FLAG_PIPE;
        case '>':
        case '<':
            return FLAG_REDIRECT;
    }
    return -1;
}

command* get_last_cmd(List* list)
{
    return list->last? (command*) list->last->data : NULL;
}

/** Public functions **/

/**
 * Parse a command line and returns a list of commands
 * 
 * @param line
 * @return 
 */
List* parse_line(char *line) 
{
    List* cmd_list = new_list();
    int has_option = 0;
    int last_was = -1;
    
    char* token = strtok(line, " \t");
    while (token) {
        node_t last_cmd_node = cmd_list->last;
        
        if (get_operator_flag(token[0]) != FLAG_NONE) {
            if ( ! cmd_list->last) {
                printf("Invalid command syntax: Operators should not precede commands!\n");
                break;
            } else {
                if (get_operator_flag(token[0]) == FLAG_PIPE) {
                   get_last_cmd(cmd_list)->flag = FLAG_PIPE;
                   last_was = 2;
                }
                if (get_operator_flag(token[0]) == FLAG_REDIRECT) {
                   get_last_cmd(cmd_list)->flag = FLAG_REDIRECT;
                   last_was = 3;
                }
                has_option = 0;
            }
        } else if (token[0] == '-' || last_was == 0) {
            if (last_cmd_node != NULL) {
                list_add(get_last_cmd(cmd_list)->args, strdup(token));
                has_option = 1;
                last_was = 1;
            } else {
                printf("Invalid command syntax: Args should not precede commands!\n");
                break;
            }
        }else {
            if (last_was == 1) {
                // If we are here it's an option value
                list_add(get_last_cmd(cmd_list)->args, strdup(token));
                last_was = 1;
            } else {
                command* cmd = new_command();
                cmd->name = strdup(token);
                cmd->cmd = has_command(token);
                cmd->args = new_list();
                if (last_was == 3) {
                    cmd->type = CMD_STRING;
                }
                list_add(cmd->args, strdup(token));
                list_add(cmd_list, cmd);
                last_was = 0;
            }
        }
        token = strtok(NULL, " \t");
    }

    return cmd_list;
}