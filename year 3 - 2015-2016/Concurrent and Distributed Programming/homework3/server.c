#include <unistd.h>
#include <stdlib.h>
#include <fnmatch.h>
#include <string.h>
#include <stdio.h>
#include <fcntl.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <dirent.h>
#include <pthread.h>
#include "utils.h"

#define LISTEN_CHANNEL "/tmp/server_listen"
#define RESPONSE_CHANNEL "/tmp/server_response"

void walk_dir(char* path, char* pattern, int* total) 
{
	struct dirent *ent;
	DIR *dir = opendir(path);
	
	if (dir != NULL) {
		while ((ent = readdir(dir)) != NULL) {
			if (strcmp(ent->d_name, ".") == 0 || strcmp(ent->d_name, "..") == 0) {
				continue;
			}

			char entpath[6048];
			sprintf(entpath, "%s/%s", path, ent->d_name);

			if (is_dir(entpath)) {
				walk_dir(entpath, pattern, total);
			} else {
				// If the file has a valid mask, count
				if (fnmatch(pattern, entpath, 0) == 0) {
					(*total)++;
				}
			}
		}
		closedir(dir);
	}
}


void* request(void* data)
{
	char* channel = data;
	char client_server[BUFFER_SIZE], server_client[BUFFER_SIZE], mask[BUFFER_SIZE], path[BUFFER_SIZE];
	int in, out, total = 0;

	sprintf(client_server, "/tmp/%s_c_s", channel);
	sprintf(server_client, "/tmp/%s_s_c", channel);

	printf("Opening FIFO `%s` for READ\n", client_server);
	//

	in = fifo(client_server, O_RDONLY);

	// Receive file mask
	receive_message(in, mask);
	printf("In thread mask: %s\n", mask);

	// Receive file path
	receive_message(in, path);
	printf("In thread path: %s\n", path);
	close(in);

	// Count recursive the files that have the right mask
	walk_dir(path, mask, &total);

	// Send the total number of files to the client
	out = fifo(server_client, O_WRONLY);
	write(out, &total, sizeof(int));
	close(out);

	unlink(client_server);
	unlink(server_client);

	return data;
}

void accept(int fd) 
{
	char data[512], *channel;
	int n = read(fd, data, sizeof(data));
	close(fd);

	if (n > 0) {
		data[n] = 0;
		printf("Accepted client `%s` on process %d\n", data, getpid());

		// Start thread
		pthread_t tid;

		channel = strdup(data);
		pthread_create(&tid, NULL, request, (void*) channel);
	}
}

int main() 
{
	printf("Opening FIFO `%s` on process %d\n", LISTEN_CHANNEL, getpid());
	int in = fifo(LISTEN_CHANNEL, O_RDONLY);

	while (1) {
		// Accept a new client connection
		accept(in);

		// Reopen the listening
		in = fifo(LISTEN_CHANNEL, O_RDONLY);
	}

	exit(EXIT_SUCCESS);
}