
#ifndef BASE64_H_
#define BASE64_H_

char* xor_cipher(const char* text, const char* salt);
int base64_encode(const void* data_buf, unsigned int dataLength, char* result, unsigned int resultSize);
#endif