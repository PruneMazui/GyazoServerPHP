#!/bin/bash

# run first/*.sql
echo "--------------------------------------------------"
echo "run first/*.sql"

files="./first/*.sql"
for filepath in ${files}
do
    echo "mysql < "${filepath}
    mysql < ${filepath}
done


# run setup/*.sql > gyazo_php
echo "--------------------------------------------------"
echo "run latest/*.sql > gyazo_php"

files="./setup/*.sql"
for filepath in ${files}
do
    echo "mysql gyazo_php < "${filepath}
    mysql gyazo_php < ${filepath}
done

# run setup/*.sql > test_property
echo "--------------------------------------------------"
echo "run latest/*.sql > test_gyazo_php"

files="./setup/*.sql"
for filepath in ${files}
do
    echo "mysql test_gyazo_php < "${filepath}
    mysql test_gyazo_php < ${filepath}
done

echo "done"
