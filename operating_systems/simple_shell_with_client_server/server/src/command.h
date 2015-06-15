#ifndef __COMMAND_H

#include <sys/types.h>
#include "utils.h"

typedef int (* cmd_fct) (int argc, char** argv, int in, int out);

typedef struct {
    char* name;
    cmd_fct callback;
} cmd_callback;

typedef enum {
    FLAG_NONE        = -1,
    FLAG_PIPE        = 0,
    FLAG_REDIRECT    = 1,
} FLAG;

typedef enum {
    CMD_DEFAULT,
    CMD_STRING
} CMD_TYPE;

typedef struct {
    char* name;
    int in;
    int out;
    int ret;
    List* args;
    cmd_callback* cmd;
    FLAG flag;
    CMD_TYPE type;
    pid_t pid;
} command;

extern List* registered_cmds;

command* new_command();
void free_command();
int run_command();
int command_exists(command* cmd);
void register_command(char* name, cmd_fct callback);
cmd_callback* has_command(char * name);
int run_cmd(command* cmd);
void run_line(List* line);
#endif
