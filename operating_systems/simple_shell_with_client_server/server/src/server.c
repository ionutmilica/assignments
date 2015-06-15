#include <string.h>
#include <ctype.h>
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#include <sys/sendfile.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <pthread.h>

#include "init.h"
#include "parser.h"
#include "utils.h"

/**
 * Creates a new socket for the server
 * 
 * @return 
 */
int create_socket() 
{
    int i, socket_desc = socket(AF_INET, SOCK_STREAM, 0);
    if (socket_desc == -1) {
        printf("Error in creating the socket !\n");
        exit(1);
    }
    if ((setsockopt(socket_desc, SOL_SOCKET, SO_REUSEADDR, &i, sizeof(i))) == -1) {
        printf("Cannot set SO_REUSEADDR option to socket !\n");
	exit(1);
    }
    
    printf("Server socket was created successfully! \n");
    
    return socket_desc;
}

/**
 * Start server on a specified port
 * 
 * @param socket_fd
 * @param port
 * @return 
 */
int bind_server(int socket_fd, int port)
{
    struct sockaddr_in server;
     
    server.sin_family = AF_INET;
    server.sin_addr.s_addr = INADDR_ANY;
    server.sin_port = htons(port);
     
    if( bind(socket_fd,(struct sockaddr *)&server , sizeof(server)) < 0) {
        perror("Server cannot be started. Error");
        exit(1);
    }
    
    printf("Server started at %s:%d !\n", "127.0.0.1", port);
}

/**
 * Send a text message
 * 
 * @param socket
 * @param message
 * @return 
 */
int send_message(int socket, char* message)
{
    if (send(socket, message, strlen(message), 0) < 0) {
        printf("Cannot send message `%s` to socket %d!\n", message, socket);
        exit(1);
    }
}

/**
 * Send an integer to the client
 * 
 * @param socket
 * @param message
 * @param size
 * @return 
 */
int send_message_int(int socket, int* message, size_t size) {
    if (send(socket, message, size, 0) < 0) {
        printf("Cannot send message `%s` to socket %d!\n", message, socket);
        exit(1);
    }
}

void clean_string(char* str) {
    int i;
    for (i = 0; i < strlen(str); i++) {
        if ((int) str[i] > 127) {
            str[i] = 0;
            break;
        }
    }
}

/*
 * This will handle connection for each client
 * */
void* instance_handler(void *socket_desc)
{
    int fd[2];
    List* cmd_line;
    int socket_fd = *(int*) socket_desc;
    int read_size;
    char message[2000];
    char tmp[200];
    
    register_commands();

    while( (read_size = recv(socket_fd , message , 2000 , 0)) > 0 ) {
        printf("Client said: %s\n", message);
        pipe(fd);

        if (fork() == 0) {
            close(fd[0]);
            dup2(fd[1], STDOUT_FILENO);
            cmd_line = parse_line(message);
            run_line(cmd_line);
            close(fd[1]);
            exit(0);
        } else {
            int n;
            close(fd[1]);
            while ((n = read(fd[0], tmp, 30)) > 0) {
                tmp[n] = 0;
                clean_string(tmp);
                send_message_int(socket_fd, &n, sizeof n);
                send_message(socket_fd, tmp);
            } 
            n = -1;
            send_message_int(socket_fd, &n, sizeof n);

            close(fd[0]);
        }

        memset(message, 0, sizeof(message));
    }
     
    if(read_size == 0) {
        puts("Client disconnected");
        fflush(stdout);
    } else if(read_size == -1) {
        perror("Message receiving failed");
    }
    free(socket_desc);
     
    return 0;
}
 
int main(int argc , char *argv[])
{
    int socket_fd, port = 6666, client_socket , c , *tmp_socket;
    struct sockaddr_in client;
    
    if (argc > 1) {
        port = atoi(argv[1]);
    }
    
    socket_fd = create_socket();
    bind_server(socket_fd, port);
    listen(socket_fd, 3);
     
    printf("Waiting for clients...\n");
    
    c = sizeof(struct sockaddr_in);
    
    while( (client_socket = accept(socket_fd, (struct sockaddr *)&client, (socklen_t*)&c)) )
    {
        pthread_t instance_thread;
        printf("Received a new connection: Accepted !\n");
         
        tmp_socket = malloc(1);
        *tmp_socket = client_socket;
         
        if( pthread_create( &instance_thread, NULL, instance_handler, (void*) tmp_socket) < 0) {
            perror("Thread creation failed");
            return 1;
        }
    }
     
    if (client_socket < 0) {
        perror("Accept failed");
        return 1;
    }
     
    return 0;
}
