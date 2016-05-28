#include "utils.h"

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

	return open(filename, mode);
}

void remove_newline(char* string) {
	int len = strlen(string);
	if (string[len - 1] == '\n') {
		string[len - 1] = '\0';
	}
}