#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <signal.h>
#include <string.h>
#include "client.h"

/** From readline library **/
#include <readline/readline.h>
#include <readline/history.h>

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
    return socket_desc;
}

int connect_server(int socket, char* ip, int port) {
    int status;
    struct sockaddr_in server;
    
    server.sin_addr.s_addr = inet_addr(ip);
    server.sin_family = AF_INET;
    server.sin_port = htons(port);
    
    status = connect(socket, (struct sockaddr*) &server, sizeof(server));
    
    if (status < 0) {
        perror("Connection to server failed! Error");
        exit(1);
    }
    
    return status;
}

int send_message(int socket, char* message)
{
    if (send(socket, message, strlen(message), 0) < 0) {
        printf("Cannot send message `%s` to socket %d!\n", message, socket);
        exit(1);
    }
}

int receive_message(int socket, char* message, size_t size) 
{
    size_t rsize = recv(socket , message, size, 0);
    
    if (rsize < 0) {
        puts("Cannot receive message from server !\n");
        exit(1);
    }
    
    return rsize;
}

int receive_int(int socket, int* message, size_t size) {
    size_t rsize = recv(socket , message, size, 0);
    
    if (rsize < 0) {
        puts("Cannot receive message from server !\n");
        exit(1);
    }
    
    return rsize;
}

/**
 * Pings the server and kill the client if it's closed
 * 
 * @param socket
 */
void ping_server(int socket) {
    if (fork() == 0) {
        char tmp;
        size_t rec;
        
        while (1) {
            rec = recv(socket, &tmp, 1, MSG_PEEK);
            if (rec == 0) {
                kill(getppid(), SIGINT);
                break;
            }
        }
    }
}

int main(int argc , char *argv[])
{
    char* ip, *input;
    int port = 6666;
    int socket;
    char reply[31];
    
    if (argc > 1) {
        ip = strdup(argv[1]);
        if (argc > 2) {
            port = atoi(argv[2]);
        }
    } else {
        ip = strdup("127.0.0.1");
    }
     
    socket = create_socket();
    connect_server(socket, ip, port);
 
    printf("Client connected to server %s:%d.\n", ip, port);
    
    ping_server(socket);
    
    while (1) {
        int len;
        input = readline("$> ");
        if ( ! input)
            break;

        if (strlen(input) > 0) {
            add_history(input);
            
            send_message(socket, input);
            receive_int(socket, &len, sizeof len);

            while (len > 0) {
                receive_message(socket, reply, len);
                reply[len] = 0;
                printf("%s", reply);
                memset(reply, 0, sizeof(reply));
                receive_int(socket, &len, sizeof len);
                if (len <= 0) {
                    printf("\n");
                }
            }        
        }

        free(input);
    }

    close(socket);
    return 0;
}