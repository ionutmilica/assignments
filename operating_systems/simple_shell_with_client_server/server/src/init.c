#define _XOPEN_SOURCE 500
#include "init.h"
#include "utils.h"
#include <string.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <dirent.h>
#include <sys/utsname.h>
#include <ftw.h>

/**
 * Info about the terminal
 * 
 * @param argc
 * @param argv
 * @param in
 * @param out
 * @return 
 */
int cmd_version(int argc, char** argv, int in, int out)
{
    prepare_stream();
    struct utsname un;
    uname(&un);
    print("Terminal 0.9\n");
    print("Author: Milica Ionut Catalin\n");
    print("System: %s\n", un.version);
    print("Running on %s processor !\n", un.machine);
    close_stream();
    return 0;
}

/**
 * Help command
 * 
 * @param argc
 * @param argv
 * @param in
 * @param out
 * @return 
 */
int cmd_help(int argc, char** argv, int in, int out)
{  
    prepare_stream();
    int i = 0, found = 0;
    static char* text[] = {
        "dir: dir\nDisplays all the files from the current working directory!",
        "rm: rm [-v] [-r] [i]\n Removes a file !",
        "mv: mv [-i] [-t] [-S]\n Moves a file to a given destination",
        "help: help [cmd]\n Displays for a single or for the all commands info about it/them.",
        "setwd: setwd [path]\n Changes the current working directory !",
        "clear: clear\n Clear the screen !",
        "exit: exit \n Closes the terminal",
    };
    
    for (i = 0; i < 7; i++) {
        if (argc == 1) {
            print("* %s\n", text[i]);
        } else if (argc > 1 && strstr(text[i], argv[1]) != NULL) {
            print("* %s\n", text[i]);
            found = 1;
            break;
        }
    }
    
    if (argc > 1 && ! found) {
        print("Help for command `%s` not found !\n", argv[0]);
    }
    
    close_stream();
    return 0;
}

/**
 * Current directory contents
 * 
 * @param argc
 * @param argv
 * @param in
 * @param out
 * @return 
 */
int cmd_dir(int argc, char** argv, int in, int out)
{
    prepare_stream();

    struct dirent *dir_entry;
    DIR *dir;
    char cwd[1024];

    if (getcwd(cwd, sizeof(cwd)) == NULL) {
        print("Cannot get current working directory !");
        close_stream();
        return 1;
    }
    
    dir = opendir(cwd);
    
    if (dir == NULL) {
        print("Cannot open current working directory !");
        close_stream();
        return 2;
    }
    
    while ((dir_entry = readdir(dir)) != NULL) {
        if (strcmp(dir_entry->d_name, ".") && strcmp(dir_entry->d_name, "..")) {
            print("%s ", dir_entry->d_name);
        }
    }
    print("\n");
    close_stream();
    return 0;
}

/**
 * Mv command
 * 
 * @param argc
 * @param argv
 * @param in
 * @param out
 * @return 
 */
int cmd_mv(int argc, char** argv, int in, int out) 
{
    prepare_stream();
    int c, i, mv_prompt = 0, has_target = 0, opt, last_source = 0;
    char* target = NULL;
    
    while ((c = getopt(argc, argv, "it:s")) != -1) {
        switch (c) {
            case 'i':
                mv_prompt = 1;
                break;
            case 't':
                has_target = 0;
                target = strdup(optarg);
                has_target = 1;
                break;
            case 'S':
                printf("A random option that does nothing !\n");
                break;
            default: 
                print("mv: Usage: mv [-i] [-t TARGET] [-s] FILE\n");
                close_stream();
                optind = 0;
                return -2;
        }
    }
    
    opt = optind;
    optind = 0;
    
    if ( ! has_target && argc - opt <= 1) {
        print("mv: You should choose a destination for your file !\n");
        return 1;
    }
    
    if (has_target) {
        last_source = argc;
    } else {
        target = strdup(argv[argc - 1]);
        last_source = argc - 1;
    }
    
    for (i = opt; i < last_source; i++) {
        char ask = 'n';
        if (mv_prompt) {
            print("mv: move `%s`? ", argv[i]);
            scan("%c", &ask);
        }
        if (!mv_prompt || ask == 'y') {
            struct stat st;
            stat(target, &st);
            if (S_ISDIR(st.st_mode)) {
                if (rename(argv[i], concat_paths(target, argv[i]))) {
                    print("mv: Cannot move `%s`\n", argv[i]);
                }
            } else {
                if (rename(argv[i], target)) {
                    print("mv: Cannot move `%s`\n", argv[i]);
                }
            }
        }
    }
    
    close_stream();
    return 0;
}



FILE* rm_verbose = NULL;
int rm_prompt = 0;
FILE* rm_fdin = NULL;
FILE* rm_fdout = NULL;
/**
 * Callback for nftw function, used for files and directories deletion.
 * 
 * @param fpath
 * @param sb
 * @param typeflag
 * @param ftwbuf
 * @return 
 */
int remove_callback(const char *fpath, const struct stat *sb, int typeflag, struct FTW *ftwbuf)
{
    int rv; 
    char ask = 'n';

    if (rm_prompt) {
        fprintf(rm_fdout, "rm: remove `%s`? ", fpath);
        fscanf(rm_fdin, "%c", &ask);
    }
    if (!rm_prompt || ask == 'y') {
        rv = remove(fpath);
        if (rv) {
            perror(fpath);
        } else {
            if (rm_verbose != NULL) {
                fprintf(rm_verbose, "`%s` removed !\n", fpath);
            }
        }        
    }

    return rv;
}

int cmd_rm(int argc, char** argv, int in, int out)
{
    prepare_stream();
    rm_fdin = fdin;
    rm_fdout = fdout;
    int c, i, opt;
    int verbose = 0, delete_dir = 0;
    
    while ((c = getopt(argc, argv, "ivrR")) != -1) {
        switch (c) {
            case 'i':
                rm_prompt = 1;
                break;
            case 'v':
                verbose = 1;
                rm_verbose = fdout;
                break;
            case 'r':
            case 'R':
                delete_dir = 1;
                break;
            default: 
                print("rm: Usage: rm [-v] [-r] [-i] FILE\n");
                close_stream();
                return -2;
        }
    }
    opt = optind;
    optind = 0;
    
    if (opt >= argc) {
        print("rm: missing operand !\n");
        close_stream();
        return 1;
    }
    
    for (i = opt; i < argc; i++) {
        struct stat buf;
        stat(argv[i], &buf);
        /************************ Found a "*". Prepare to delete current directory content **********************************/
        if (strcmp(argv[i], "*") == 0) {
            /** Remove all files from this directory **/
            DIR* dir;
            struct dirent* dir_entry = NULL;
            char cwd[1024];
            
            if (getcwd(cwd, sizeof(cwd)) == NULL) {
                print("Cannot get current working directory !");
                close_stream();
                return 1;
            }       
            dir = opendir(cwd);     
            if (dir == NULL) {
                print("Cannot open current working directory !");
                close_stream();
                return 2;
            }
            while ((dir_entry = readdir(dir)) != NULL) {
                if (strcmp(dir_entry->d_name, ".") && strcmp(dir_entry->d_name, "..")) {
                    struct stat buf;
                    stat(dir_entry->d_name, &buf);
                    if (S_ISREG(buf.st_mode)) {
                        char ask = 'n';
                        if (rm_prompt) {
                            printf("rm: remove file `%s`? ", dir_entry->d_name);
                            fscanf(fdin, "%c", &ask);
                        }
                        if (!rm_prompt || ask == 'y') {
                            if (remove(argv[i]) < -1) {
                                print("rm: cannot delete `%s` !\n", dir_entry->d_name);
                            }
                            if (verbose == 1) {
                                print("`%s` removed !\n", dir_entry->d_name);
                            }   
                        }
                    } else if (S_ISDIR(buf.st_mode)) {
                        if (delete_dir) {
                            nftw(dir_entry->d_name, remove_callback, 64, FTW_DEPTH | FTW_PHYS);
                        } else {
                            print("rm: cannot remove `%s`: It's a directory !\n", dir_entry->d_name);
                        }
                    }
                }
            }
            break;
        /************************ Found a file. Prepare to delete it **********************************/
        } else if (S_ISREG(buf.st_mode)) {
            char ask = 'n';

            if (rm_prompt) {
                printf("rm: remove file `%s`? ", argv[i]);
                fscanf(fdin, "%c", &ask);
            }
            
            if (!rm_prompt || ask == 'y') {
                if (remove(argv[i]) < -1) {
                    print("rm: cannot delete `%s` !\n", argv[i]);
                }
                if (verbose == 1) {
                    print("`%s` removed !\n", argv[i]);
                }   
            }
        /************************ Found a directory **********************************/
        } else if (S_ISDIR(buf.st_mode)) {
            if (delete_dir) {
                nftw(argv[i], remove_callback, 64, FTW_DEPTH | FTW_PHYS);
            } else {
                print("rm: cannot remove `%s`: It's a directory !\n", argv[i]);
            }
        } else {
            print("rm: cannot delete `%s`: File does not exist !\n", argv[i]);
        }      
    }
    
    close_stream();
    return 0;
}

/**
 * A small cd replacement
 * 
 * @param argc
 * @param argv
 * @param in
 * @param out
 * @return 
 */
int cmd_setwd(int argc, char** argv, int in, int out) {
    prepare_stream();
    if (argc < 2 || argc > 2) {
        print("Usage: setwd [Directory] !\n");
        close_stream();
    } else {
        struct stat buf;
        stat(argv[1], &buf);
        if (S_ISDIR(buf.st_mode)) {
            chdir(argv[1]);
        } else {
            print("setwd: %s is not a directory !\n", argv[1]);
            close_stream();
            return 1;
        }
    }
    close_stream();
    return 0;
}

int cmd_exit(int argc, char** argv, int in, int out)
{
    exit(0);
    return 0;
}

int cmd_clear(int argc, char** argv, int in, int out)
{
    system("clear");
    return 0;
}

void register_commands()
{
    register_command("version", cmd_version);
    register_command("exit", cmd_exit);
    register_command("clear", cmd_clear);
    register_command("setwd", cmd_setwd);

    /** homework commands **/
    register_command("help", cmd_help);
    register_command("dir", cmd_dir);
    register_command("rm", cmd_rm);
    register_command("mv", cmd_mv);
}
