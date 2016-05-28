#ifndef UTILS_H
#include <sys/types.h>

#define BUFFER_SIZE 512

int file_exists(const char* filename);
int fifo(const char* filename, mode_t mode);
void remove_newline(char* string);
int my_rand(int max);
int is_dir(const char *path);
int regex_match(char* regex, char* str);

int send_message(int fd, char* buffer, size_t size);
int receive_message(int fd, char* buffer);

#endif