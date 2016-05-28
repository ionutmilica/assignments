#include "Helper.h"

int Helper::StrToInt(std::string str)
{
	std::stringstream ss;
	int num;
	ss << str;
	ss >> num;
	return num;
}

std::string Helper::CharToStr(char c)
{
	std::stringstream ss;
	std::string myStr;
	ss << c;
	ss >> myStr;
	return myStr;	
}

std::string Helper::removeSpace(std::string str)
{
	for (size_t i = 0; i < str.size(); ++i)
	{
		if (str[i] == ' ')
		{
			str.erase(str.begin() + i);
		}
	}
	return str;
}


double Helper::solveBinaryOp(double a, double b, std::string op)
{
	if (op == "+")
		return a + b;

	if (op == "-")
		return a - b;
	
	if (op == "*")
		return a * b;
	
	if (op == "/")
	{
		if (b == 0)
			return 0;
		return a / b;
	}
	if (op == "%")
	{
		if (b == 0)
			return 0;
		return fmod(a, b);
	}
	if (op == "^")
		return pow(a, b);
	return 0;
}