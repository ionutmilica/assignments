#ifndef UTILS_H

#include <unistd.h>
#include <stdio.h>
#include <string.h>
#include <fcntl.h>
#include <sys/types.h>
#include <sys/stat.h>

#define BUFFER_SIZE 512

int file_exists(const char* filename);
int fifo(const char* filename, mode_t mode);
void remove_newline(char* string);

#endif