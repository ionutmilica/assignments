#ifndef INIT_H
#define	INIT_H

#include "command.h"

#define prepare_stream() FILE *fdout = stdout, *fdin = stdin;                 \
    if (out != STDOUT_FILENO) fdout = fdopen (out, "a"); else fdout = stdout; \
    if (in != STDIN_FILENO) fdin = fdopen(out, "r"); else fdin = stdin;

#define close_stream() if (out != STDOUT_FILENO) fclose (fdout); if (in != STDIN_FILENO) fclose(fdin);

#define print(...) fprintf(fdout,__VA_ARGS__)
#define scan(...)  fscanf(fdin, __VA_ARGS__)

void register_commands();

#endif	/* INIT_H */

