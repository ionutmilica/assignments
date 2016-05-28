#include "utils.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

List* new_list()
{
    List* tmp = (List*) malloc(sizeof(List));
    tmp->first = NULL;
    tmp->last  = NULL;
    tmp->counter = 0;

    return tmp;
}

/**
 * Add a new generic element to the list
 * 
 * @param list
 * @param data
 */
void list_add(List* list, void * data)
{
    node_t tmp = (node_t) malloc(sizeof(struct Node));
    tmp->data = data;
    
    if (list->last) {
        tmp->prev = list->last;
        list->last->next = tmp;
        list->last = tmp;
    } else {
        tmp->prev = NULL;
        tmp->next = NULL;
        list->first = tmp;
        list->last = tmp;
    }
    
    list->counter++;
}

/**
 * Check if a given node is the first list element
 * 
 * @param list
 * @param node
 * @return 
 */
int list_is_first(List* list, node_t node) {
    if (list->first == node) {
        return 1;
    }
    return 0;
}

/**
 * Reset list
 * 
 * @param list
 */
void list_free(List* list)
{
    node_t current;
    node_t next;
    for (current = list->first; current != NULL; current = next) {
        next = current->next;
        free(current);
    }
}

/**
 * Concat 2 paths
 * concatPath("/home", "test") => /home/test
 * 
 * @param a
 * @param b
 * @return 
 */
char * concat_paths(const char * old, const char * new)
{
    size_t len = strlen(old) + strlen(new) + 2;
    char *out = malloc(len);
    sprintf(out, "%s/%s", old, new);
    return out;
}

