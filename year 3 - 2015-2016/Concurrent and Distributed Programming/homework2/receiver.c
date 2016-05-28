#include <unistd.h>
#include <stdlib.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <sys/types.h>
#include <sys/stat.h>
#include "utils.h"

#define FIFO_SEND_NAME "/tmp/out"
#define FIFO_RECEIVE_NAME "/tmp/ion"

int main()
{
	int fd, out, total = 0;
	char filename[255];
	
	printf("Opening FIFO `%s` on process %d\n", FIFO_RECEIVE_NAME, getpid());
	fd = fifo(FIFO_RECEIVE_NAME, O_RDONLY);

	if (fd == -1) {
		fprintf(stderr, "Could not create fifo %s\n", FIFO_RECEIVE_NAME);
		exit(EXIT_FAILURE);
	}

	// Receive the filename
	memset(filename, '\0', sizeof(filename));
	read(fd, filename, sizeof(filename));
	printf("Received `%s` filename on process %d\n", filename, getpid());
	close(fd);

	printf("Process %d opening FIFO on file %s\n", getpid(), filename);
	out = fifo(FIFO_SEND_NAME, O_WRONLY);

	if (file_exists(filename)) {
		int file = open(filename, O_RDONLY), n;
		char buffer[BUFFER_SIZE];

		// Send the file into the second pipe
		while ((n = read(file, buffer, sizeof(buffer))) && n > 0) {
			printf("Writing %d bytes\n", n);
			write(fd, buffer, n);
			total += n;
		}
	}
	close(out);
	printf("Process %d finished sending the file (%d bytes)!\n", getpid(), total);
	exit(EXIT_SUCCESS);
}