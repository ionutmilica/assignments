#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>

#include <sys/stat.h>
#include <sys/sendfile.h>
#include <sys/socket.h>
#include <arpa/inet.h>

#include "utils.h"

void connect_server(int sock, const char* ip, int port)
{
	int status;
    struct sockaddr_in server;
    server.sin_addr.s_addr = inet_addr(ip);
    server.sin_family = AF_INET;
    server.sin_port = htons(port);
    
    status = connect(sock, (struct sockaddr*) &server, sizeof(server));
    
    if (status < 0) {
        perror("Connection to server failed! Error");
        exit(1);
    }
}

int file_size(const char* filename)
{
	struct stat st;
	
	if (stat(filename, &st) == -1) {
		printf("File `%s` stat failed!\n", filename);
		exit(1);
	}

	return st.st_size;
}

int main(int argc, char** argv)
{
	if (argc != 4) {
		printf("Usage: ./client [ip] [port] [file]\n");
		return 1;
	}

	const char *ip, *filename;
	int sock, port;
    int file_fd, n, size, type, val;
    char buffer[BUFFER_SIZE];

    ip = argv[1];
    port = atoi(argv[2]);
    filename = argv[3];

    file_fd = open(filename, O_RDONLY);

    if (file_fd == -1) {
    	printf("Could not open `%s` file!\n", filename);
    	exit(1);
    }

    sock = create_socket();
    connect_server(sock, ip, port);
    printf("Client connected to server %s:%d.\n", ip, port);

	size = file_size(filename);

	// Send filesize
	write(sock, &size, sizeof(size));

	// Send the file
	while ((n = read(file_fd, buffer, sizeof(buffer))) && n > 0) {
		printf("Sent `%d` bytes to server.\n", n);
		write(sock, buffer, n);
	}    

	close(file_fd);

	// Read the response type and value
	read(sock, &type, sizeof(type));
	read(sock, &val, sizeof(val));

	switch (type) {
		case 0: 
			printf("Server determined this is a text file, containing %d lines of text.\n", val);
		break;
		case 1: 
			printf("Server determined that the file is a binary file, with the first binary character appearing at position %d.\n", val);
		break;
	}
    close(sock);

	return 0;
}