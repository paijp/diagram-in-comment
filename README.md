# diagram-in-code

- You can draw a diagram using the comment block in your code.
- This command generates this diadram.

```
 $ php diacode.php <syntaxdiagram.php >syntaxdiagram.html
```

https://paijp.github.io/diagram-in-code/syntaxdiagram.html

- And you can generate this diagram: https://paijp.github.io/smallest-touchpanel-ui/pic32mx/port_circuit.html from this code: https://github.com/paijp/smallest-touchpanel-ui/blob/main/pic32mx/lcdtp.c .

- If you put this code into stdin, 

```
/*type-a
100
110
*/

/*type-b
200
*/

/*type-a
300
*/
```

- diacode.php will do this.

```
$ php sample/type-a.php <<EOO
/*sample/type-a
100
110
*/

/*sample/type-b
200
*/

/*sample/type-a
300
*/
EOO

$ php sample/type-b.php <<EOO
/*sample/type-b
200
*/

/*sample/type-a
300
*/
EOO

$ php sample/type-a.php <<EOO
/*sample/type-a
300
*/
EOO
```

- Each script should process the block before the first '*/'.
- Because some scripts may process the next block of code, or the entire code.

- diacode.php only requires stdin and stdout, so you can run these scripts under Docker.
