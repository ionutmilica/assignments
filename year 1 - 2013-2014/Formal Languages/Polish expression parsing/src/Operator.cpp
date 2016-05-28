#include "Operator.h"

/** Static member initializer **/

Operator::OpMap Operator::initMap()
{
	OpMap ops;
	ops["("] = 0;
	ops[")"] = 0;
	ops["+"] = 1;
	ops["-"] = 1;
	ops["*"] = 2;
	ops["/"] = 2;
	ops["%"] = 2;
	ops["^"] = 3;

	return ops;
}

Operator::OpMap Operator::operators = Operator::initMap();

/** End dirty little hack **/

Operator::Operator() {}

Operator::Operator(std::string op)
{
	this->key = op;
}

/** 
 * Checks if a given string is an operator
 * @param std::string op
 * return boolean
 */

bool Operator::isOperator(std::string op)
{
	return Operator::operators.count(op) > 0;
}

/** 
 * Operator setter.
 * @param std::string op
 * return void
 */

void Operator::setKey(std::string op)
{
	this->key = op;
}

/** 
 * Verifica daca operatorul se evaluzeaza din dreapta spre stanga
 * return boolean
**/

bool Operator::isRightToLeft()
{
	return this->key == "^" ? true : false;
}

/** 
 * Get operator key
 * return std::string 
**/

std::string Operator::getKey()
{
	return this->key;
}

/** 
 * == Operator override usefull for comparations like: 
 * Operator op("+");
 * if (op == "+")
 * 	  cout << "Yeee !";
 * @param std::string op
 * return boolean
 */

bool Operator::operator==(std::string op)
{
	return Operator::operators[this->key] == Operator::operators[op];
}

/** 
 * == Operator override usefull for comparations like: 
 * Operator op("+");
 * if (op != "+")
 * 	  cout << "Noo !";
 * @param std::string op
 * return boolean
 */
bool Operator::operator!=(std::string op)
{
	return Operator::operators[this->key] != Operator::operators[op];
}

/** 
 * Override lesser operation on operators
 * @param std::string op
 * return boolean
 */

bool Operator::operator<(std::string op)
{
	return Operator::operators[this->key] < Operator::operators[op];
}

/** 
 * Override lesser or equal operation on operators
 * @param std::string op
 * return boolean
 */

bool Operator::operator<=(std::string op)
{
	return Operator::operators[this->key] <= Operator::operators[op];
}


/** 
 * Override greatter operation on operators
 * @param std::string op
 * return boolean
 */
bool Operator::operator>(std::string op)
{
	return Operator::operators[this->key] > Operator::operators[op];
}

/** 
 * Override greatter or equal operation on operators
 * @param std::string op
 * return boolean
 */
bool Operator::operator>=(std::string op)
{
	return Operator::operators[this->key] >= Operator::operators[op];
}

/** 
 * Get operator priority
 * return int 
**/

int Operator::getPriority()
{
	return Operator::operators[this->key];
}