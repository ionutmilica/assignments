#ifndef EVALUATOR_H
#define EVALUATOR_H

#include "Operator.h"
#include "Helper.h"
#include <stack>

class Evaluator
{
	std::string expression;
public:
	Evaluator();
	Evaluator(std::string);
	double evaluate();
};

#endif