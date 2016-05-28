#ifndef HELPER_H
#define HELPER_H

#include <iostream>
#include <sstream>
#include <math.h>

class Helper
{
public:
	static int StrToInt(std::string);
	static std::string CharToStr(char);
	static std::string removeSpace(std::string);
	static double solveBinaryOp(double, double, std::string);
};

#endif