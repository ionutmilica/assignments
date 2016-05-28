#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "encryption.h"

#define EQ(a, b) (strcmp(a, b) == 0)

void print_usage() {
	printf("Usage: ./homework1 -u user [-p pass]\n");
}

static char* SALT_KEY = "#2ED$ '@";

// Accept -u user [-p pass]
int main(int argc, char** argv) {
	if (argc != 3 && argc != 5) {
		print_usage();
		return 1;
	}

	char *user, *pass = "test";
	
	if (EQ(argv[1], "-u")) {
		user = argv[2];
	} else {
		print_usage();
		return 2;
	}

	if (argc == 5 && EQ(argv[3], "-p")) {
		pass = argv[4];
	}

	char *p = xor_cipher(pass, SALT_KEY);

	char result[1024];
	base64_encode(p, strlen(p), result, sizeof(result));

	printf("Your password: %s\n", result);

	free(p);

	return 0;
}