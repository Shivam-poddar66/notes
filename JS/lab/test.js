import  { getSystemInfo }  from  './utils.js';
 
console.log('=== Node.js Runtime Verification ===');
const info = getSystemInfo();

console.table(info);
console.log(`\nStatus: Node.js setup is functioning correctly!`);