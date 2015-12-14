#include "Parser.h"

void printStack(std::stack<Operator> ops)
{
	while (ops.size() > 0)
	{
		std::cout << ops.top().getKey() << " ";
		ops.pop();
	}
}

Parser::Parser(std::string expression)
{
	this->expression = Helper::removeSpace(expression);
}

void Parser::setDebug(bool debug)
{
	this->debug = (bool) debug;
}

std::string Parser::parse()
{
	std::stack<Operator> operators;
	std::string output = "";
	std::string token = "";

	for (size_t i = 0; i < this->expression.size(); ++i)
	{
		token = Helper::CharToStr(expression[i]);

		if ( ! Operator::isOperator(token))
		{
			output += token + " ";
		}
		else
		{
			if (operators.size() > 0)
			{
				Operator lastOperator = operators.top();

				/** If the token is a left paranthesis  just push the operator **/

				if (token == "(")
				{
					operators.push(Operator(")"));
					continue;
				}

				/** If the token is left paranthesis pop operators to the output while we get to the left paranthesis **/

				if (token == ")")
				{
					while (lastOperator != "(")
					{
						output += lastOperator.getKey() + " ";
						operators.pop();
						lastOperator = operators.top();
					}
					operators.pop();
					continue;
				}

				/** 
				 * If the operator from the stack has greatter or equal priority (precedence)
				 * we pop operators while the condition is satisfied
				**/

				if (lastOperator >= token)
				{
					while (operators.size() > 0 && (lastOperator>= token && ! Operator(token).isRightToLeft()))
					{
						output += lastOperator.getKey() + " ";
						operators.pop();

						if (operators.size() > 0) 
							lastOperator = operators.top();
					}
				}	

				/** Just push the operator in operators stack **/

				operators.push(Operator(token));
			}
			else
			{
				/** Is the stack is empty push the operator in the operators stack. **/

				operators.push(Operator(token));
			}
		}
		
		if (debug)
		{
			printStack(operators);
			std::cout << std::endl;	
		}
	}

	/** Pop the rest of the operators to the output **/

	while (operators.size() > 0)
	{
		output += operators.top().getKey() + " ";
		operators.pop();
	}

	return output;
}