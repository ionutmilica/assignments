#ifndef OPERATOR_H
#define OPERATOR_H

#include <iostream>
#include <map>

class Operator
{
	typedef std::map<std::string, int> OpMap;
	static OpMap operators;
	std::string key;
public:
	Operator();
	Operator(std::string);
	static OpMap initMap();
	static bool isOperator(std::string);
	bool isRightToLeft();
	bool operator==(std::string);
	bool operator!=(std::string);
	bool operator<(std::string);
	bool operator<=(std::string);
	bool operator>(std::string);
	bool operator>=(std::string);
	void setKey(std::string);
	std::string getKey();
	int getPriority();
};

#endif