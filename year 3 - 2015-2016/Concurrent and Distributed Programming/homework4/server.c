#include <string.h>
#include <ctype.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#include <sys/sendfile.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <pthread.h>

#include "utils.h"

void* handler(void* socket) {
    int n, i, socket_fd = *(int*) socket;
    char buffer[BUFFER_SIZE];
    int lines = 1, binary_file = 0, found_binary_at = 0, pos = 0;
    int size, read_size = 0;

    // Read the filesize
    read(socket_fd, &size, sizeof(size));

    printf("File size: %d\n", size);

    do {
        memset(buffer, '\0', sizeof(char) * BUFFER_SIZE);

        n = read(socket_fd, buffer, sizeof(buffer));
        printf("Received `%d` bytes from client\n", n);

        for (i = 0; i < n && ! binary_file; i++) {
            if (buffer[i] == 3) {
                printf("END OF TEXT!\n");
            }
            if (buffer[i] == '\n') {
                lines++;
            } else if (is_binary_char(buffer[i])) {
                binary_file = 1;
                found_binary_at = pos;
            }
            pos++;
        }
        read_size += n;
    } while (read_size < size && n > 0);

    // protocol
    // type of response : int
    // response value : int /*
    int type = binary_file, val = type ? found_binary_at : lines;
    printf("Sending to client: type: %d, val: %d\n", type, val);

    write(socket_fd, &type, sizeof(type));
    write(socket_fd, &val, sizeof(val));

    close(socket_fd);
    free(socket);

    return NULL;
}

void bind_server(int socket_fd, int port)
{
    struct sockaddr_in server;

    server.sin_family = AF_INET;
    server.sin_addr.s_addr = INADDR_ANY;
    server.sin_port = htons(port);

    if (bind(socket_fd,(struct sockaddr *)&server , sizeof(server)) < 0) {
        perror("Server cannot be started. Error");
        exit(1);
    }
    
    listen(socket_fd, 3);
}

int main(int argc, char** argv)
{
	if (argc != 2) {
		printf("Usage: ./server [port]\n");
		return 1;
	}

	int socket_fd, client_sock, sock_len, port = atoi(argv[1]);
    struct sockaddr_in client;

    socket_fd = create_socket();
    bind_server(socket_fd, port);

    printf("Server started at %s:%d !\n", "127.0.0.1", port);     
    printf("Waiting for clients...\n");

    sock_len = sizeof(struct sockaddr_in);

    while ((client_sock = accept(socket_fd, (struct sockaddr *)&client, (socklen_t*)&sock_len)) ) {
        pthread_t t;
        printf("Received a new connection: Accepted !\n");

        int* sockfd = (int*) malloc(sizeof(int));
        *sockfd = client_sock;
         
        if (pthread_create(&t, NULL, handler, (void*) sockfd) < 0) {
            perror("Thread creation failed");
            return 1;
        }
    }
     
    if (client_sock < 0) {
        perror("Accept failed");
        return 1;
    }

    close(socket_fd);

    return 0;
}