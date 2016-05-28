#include "Evaluator.h"

Evaluator::Evaluator() {}

Evaluator::Evaluator(std::string expr) 
{
	this->expression = Helper::removeSpace(expr);
}

double Evaluator::evaluate()
{
	std::stack<double> operands;

	for (size_t i = 0; i < expression.size(); i++)
	{
		std::string token = Helper::CharToStr(expression[i]);

		if (Operator::isOperator(token))
		{
			double secondOperand = operands.top();
			operands.pop();
			double firstOperand  = operands.top();
			operands.pop();

			operands.push(Helper::solveBinaryOp(firstOperand, secondOperand, token));
		}
		else
		{
			operands.push(Helper::StrToInt(token));
		}
	}

	if (operands.size() == 1)
	{
		return operands.top();
	}
	//throw std::exception("Numarul de operanzi este invalid.");
	return 0;
}