#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <fcntl.h>
#include <sys/types.h>
#include <sys/stat.h>
#include "utils.h"

#define FIFO_SEND_NAME "/tmp/ion"
#define FIFO_RECEIVE_NAME "/tmp/out"

int main()
{
	int fd, in, n, file, total = 0;
	char buffer[BUFFER_SIZE], filename[255];

	printf("Process %d opening FIFO on file %s\n", getpid(), FIFO_SEND_NAME);
	fd = fifo(FIFO_SEND_NAME, O_WRONLY);

	if (fd == -1) {
		fprintf(stderr, "Could not create fifo %s\n", FIFO_SEND_NAME);
		exit(EXIT_FAILURE);
	}

	// Send the filename of the file that should be received
	printf("Enter the filename: ");
	fgets(filename, sizeof(filename), stdin);
	remove_newline(filename);
	write(fd, filename, sizeof(filename));
	close(fd);

	printf("Process %d opening FIFO on file %s\n", getpid(), FIFO_RECEIVE_NAME);
	in = fifo(FIFO_RECEIVE_NAME, O_RDONLY);

	if (fd == -1) {
		fprintf(stderr, "Could not create fifo %s\n", FIFO_RECEIVE_NAME);
		exit(EXIT_FAILURE);
	}

	file = open("output.txt", O_WRONLY | O_TRUNC | O_CREAT | O_EXCL);

	// Receive the file from the second pipe
	while ((n = read(in, buffer, sizeof(buffer))) && n > 0) {
		printf("Reading %d bytes\n", n);
		write(file, buffer, n);
		total += n;
	}
	close(file);
	close(in);

	printf("File `%s` (%d bytes) transfered on process %d\n", filename, total, getpid());
	printf("Saved as `%s`\n", "output.txt");

	exit(EXIT_SUCCESS);
}
