# C++ DroidOS

## Sololearn

## Basic Concepts
### What is C++?
* C++ is a multi-purpose programming language
![]({{site.baseurl}}/https://www.technotification.com/wp-content/uploads/2018/04/programmer-working-on-computers.jpg)
* It is used to create computer programs
* *C++ is derived from C, and is largely based on it*

### Hello, world!
* A C++ program is a series of commands or statements
* #include <iostream> - C++ offers many headers, each of which contains information needed for programs to work properly
* The program at Code Examples\Hello World needs the <iostream> header
* The number sign (#) at the beginning of a line targets the compiler's pre-processor
* In this case, #include tells the pre-processor to include the <iostream> header
* *The <iostream> header defines the standard stream objects that input and output data*
* The C++ compiler __ignores__ blank lines
* *Whitespaces, tabs and newlines are also ignored but can be used to increase the attractiveness/readability of the code*
* __using namespace std__ - tells the compiler to use the std (standard) namespace
* Program execution begins with the main function *int main()*



## Sololearn - Code Examples
### Hello, world!
#include <iostream>
using namespace std
  
int main()
 {
  cout <<"Hello world!";
  return 0;
}