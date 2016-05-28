#include "utils.h"

#include <stdio.h>
#include <stdlib.h>
#include <sys/sendfile.h>
#include <sys/socket.h>
#include <arpa/inet.h>

#include <ctype.h>

int create_socket() 
{
    int i, socket_fd = socket(AF_INET, SOCK_STREAM, 0);
    if (socket_fd == -1) {
        printf("Error in creating the socket !\n");
        exit(1);
    }
    if ((setsockopt(socket_fd, SOL_SOCKET, SO_REUSEADDR, &i, sizeof(i))) == -1) {
        printf("Cannot set SO_REUSEADDR option to socket !\n");
		exit(1);
    }

    return socket_fd;
}

int is_binary_char(char c) 
{
    int code = c;
    return (code < 26 || code > 132) && ! isspace(c);
}
