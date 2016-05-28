#include "encryption.h"
#include <string.h>
#include <inttypes.h>
#include <stdlib.h>

/**
 *Simple xor encryption using a salt(key) against the text we want to encrypt
 *
 * @return int
 **/
char* xor_cipher(const char* text, const char* salt) 
{
	int i;
	int text_len = strlen(text), salt_len = strlen(salt);

	char* buffer = (char*) malloc(text_len);

	for (i = 0; i < text_len; i++) {
		buffer[i] = text[i] ^ salt[i % salt_len];
	}
	buffer[i] = '\n';

	return buffer;
}

int base64_encode(const void* data_buf, unsigned int dataLength, char* result, unsigned int resultSize)
{
	const char base64chars[] = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	const uint8_t *data = (const uint8_t *)data_buf;
	unsigned int resultIndex = 0;
	unsigned int x;
	uint32_t n = 0;
	int padCount = dataLength % 3;
	uint8_t n0, n1, n2, n3;

	for (x = 0; x < dataLength; x += 3) {
		n = ((uint32_t) data[x]) << 16;

		if ((x + 1) < dataLength) {
			n += ((uint32_t) data[x + 1]) << 8;
		}
		if ((x + 2) < dataLength) {
	 		n += data[x + 2];
		}

		n0 = (uint8_t) (n >> 18) & 63;
		n1 = (uint8_t) (n >> 12) & 63;
		n2 = (uint8_t) (n >> 6) & 63;
		n3 = (uint8_t) n & 63;
		    
		if (resultIndex >= resultSize) {
			return 1;
		}

		result[resultIndex++] = base64chars[n0];

		if (resultIndex >= resultSize) {
			return 1;
		}

		result[resultIndex++] = base64chars[n1];

		if ((x+1) < dataLength) {
	 		if (resultIndex >= resultSize) {
		 		return 1;
		 	}
		 	result[resultIndex++] = base64chars[n2];
		}

		if ((x+2) < dataLength) {
	 		if (resultIndex >= resultSize) {
		 		return 1;
		 	}
		 	result[resultIndex++] = base64chars[n3];
		}
	}  
	if (padCount > 0) { 
  		for (; padCount < 3; padCount++) { 
     		if (resultIndex >= resultSize) {
	     		return 1;
	     	}
	     	result[resultIndex++] = '=';
	  	} 
	}

	if (resultIndex >= resultSize) {
		return 1;
   	}
	result[resultIndex] = 0;
	return 0;
}