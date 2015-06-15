#ifndef UTILS_H
#define	UTILS_H

typedef struct Node* node_t;

struct Node {
    void * data;
    node_t next;
    node_t prev;
};

typedef struct {
    node_t first;
    node_t last;
    int counter;
} List;

List* new_list();
void list_add(List* list, void* data);
void list_free();
int  list_is_first(List* list, node_t node);

char * concat_paths(const char * old, const char * new);
char* concat(char* a, char* b);

#endif	/* UTILS_H */

