#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <fcntl.h>
#include <string.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <time.h>
#include "utils.h"

#define LISTEN_CHANNEL "/tmp/server_listen"
#define RESPONSE_CHANNEL "/tmp/server_response"

char client_server[512], server_client[512];

void connect(int* out, int* in) {
	char clientId[512];
	sprintf(clientId, "%d%d", getpid(), my_rand(2000000) + getpid());
	write(*out, clientId, strlen(clientId));
	close(*out);
	
	// Now we should open to clientId_s_c and clientId_c_s
	sprintf(server_client, "/tmp/%s_s_c", clientId);
	sprintf(client_server, "/tmp/%s_c_s", clientId);

	printf("Opening `%s` for WRITING\n", client_server);
	*out = fifo(client_server, O_WRONLY);
}


int main()
{
	int out, in;
	char filemask[BUFFER_SIZE];
	char path[BUFFER_SIZE];

	srand((unsigned)time(NULL));

	printf("Process %d opening FIFO on file %s\n", getpid(), LISTEN_CHANNEL);
	out = fifo(LISTEN_CHANNEL, O_WRONLY);

	// Send client name
	connect(&out, &in);

	printf("Enter the file mask: ");
	fgets(filemask, sizeof(filemask), stdin);
	remove_newline(filemask);

	printf("Enter the path: ");
	fgets(path, sizeof(path), stdin);
	remove_newline(path);

	send_message(out, filemask, strlen(filemask));
	send_message(out, path, strlen(path));
	close(out);

	in = fifo(server_client, O_RDONLY);
	int output = 0;
	read(in, &output, sizeof(output));
	close(in);

	printf("Output: %d\n", output);

	exit(EXIT_SUCCESS);
}
