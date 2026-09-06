Q1: What do you mean by Asynchronous API? ☆☆

Answer: All APIs of Node.js library are aynchronous that is non-blocking. It essentially means a Node.js based server never waits for a API to return data. Server moves to next API after calling it and a notification mechanism of Events of Node.js helps server to get response from the previous API call.

Q2: What are the benefits of using Node.js? ☆☆
Answer: Following are main benefits of using Node.js

Aynchronous and Event Driven - All APIs of Node.js library are aynchronous that is non-blocking. It essentially means a Node.js based server never waits for a API to return data. Server moves to next API after calling it and a notification mechanism of Events of Node.js helps server to get response from the previous API call.
Very Fast - Being built on Google Chrome's V8 JavaScript Engine, Node.js library is very fast in code execution.
Single Threaded but highly Scalable - Node.js uses a single threaded model with event looping. Event mechanism helps server to respond in a non-bloking ways and makes server highly scalable as opposed to traditional servers which create limited threads to handle requests. Node.js uses a single threaded program and same program can services much larger number of requests than traditional server like Apache HTTP Server.
No Buffering - Node.js applications never buffer any data. These applications simply output the data in chunks.

Q3: Is Node a single threaded application? ☆☆
Answer: Yes! Node uses a single threaded model with event looping.

Q4: What is global installation of dependencies? ☆☆
Answer: Globally installed packages/dependencies are stored in /npm directory. Such dependencies can be used in CLI (Command Line Interface) function of any node.js but can not be imported using require() in Node application directly. To install a Node project globally use -g flag.



