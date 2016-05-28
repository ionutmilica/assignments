#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

/** From readline library **/
#include <readline/readline.h>
#include <readline/history.h>

#include "init.h"
#include "parser.h"

int main()
{
    char* input;

    register_commands();
    
    while (1) {
        input = readline("$> ");
        if ( ! input)
            break;

        if (strlen(input) > 0) {
            add_history(input);
        }

        List* cmd_line = parse_line(input);
        run_line(cmd_line);
        
        free(input);
        
    }
    return 0;
}
