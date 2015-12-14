#ifndef PARSER_H
#define PARSER_H

#include <iostream>
#include <stack>
#include <vector>
#include "Helper.h"
#include "Operator.h"

class Parser
{
	std::string expression;
	bool debug;
public:
	Parser(std::string);
	void setDebug(bool);
	std::string parse();
};

#endif