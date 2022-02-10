var driver=0;
var deductions=0;
row.deductions.forEach(function (currentValue, index, arr) {     
deductions=parseFloat(deductions)+parseFloat(currentValue.value);
});
driver=parseFloat((row.driver.rate)*row.miles);
if(row.accessory_value)
deductions+ " + " +driver+" + " +parseFloat(row.accessory_value)+"="+ (deductions + driver+parseFloat(row.accessory_value));
else
deductions+ " + " +driver+" + " +0+"="+ (deductions + driver+0);
