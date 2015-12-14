#include <iostream>
#include "Operator.h"
#include "Parser.h"
#include "Evaluator.h"

using namespace std;

int main(int argc, char** argv)
{
	string expr = "2+3*2-3";
	string revPolish = "";
	/** Console support **/

	if (argc > 1)
	{
		expr = string(argv[1]);
	}

	/** Parse the expression **/

	cout << "Parsare expresie: " << expr << endl;
	
	Parser p(expr);
	
	p.setDebug(true);
	revPolish = p.parse();
	
	cout << "Sirul polonez: " << revPolish << endl;

	/** Evaluate the expression **/

	Evaluator ev(revPolish);
	cout << "Rezultatul expresiei: " << ev.evaluate() << endl; 

	return 0;
}