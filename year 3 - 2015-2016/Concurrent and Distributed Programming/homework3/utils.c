#include "utils.h"
#include <unistd.h>
#include <stdio.h>
#include <string.h>
#include <fcntl.h>
#include <sys/stat.h>
#include <stdlib.h>
#include <dirent.h>

int file_exists(const char* filename) {
	return access(filename, F_OK) != -1;
}

int fifo(const char* filename, mode_t mode) {
	int fd;
	if ( ! file_exists(filename)) {
		fd = mkfifo(filename, 0777);
		if (fd != 0) {
			return -1;
		}
	}

	fd = open(filename, mode);

	if (fd == -1) {
		fprintf(stderr, "Could not create fifo %s\n", filename);
		exit(EXIT_FAILURE);
	}

	return fd;
}

int send_message(int fd, char* buffer, size_t size) {
	int s = size;
	write(fd, &s, sizeof(s));
	write(fd, buffer, size);
	return size;
}

int receive_message(int fd, char* buffer) {
	int size = 0;
	read(fd, &size, sizeof(size));
	read(fd, buffer, size);
	buffer[size] = 0;

	return size;
}

int is_dir(const char *path)
{
	struct stat s;
	stat(path, &s);
	return S_ISDIR(s.st_mode);
}

int my_rand(int max) {
	return (rand() % max) + 1;
}

void remove_newline(char* string) {
	int len = strlen(string);
	if (string[len - 1] == '\n') {
		string[len - 1] = '\0';
	}
}