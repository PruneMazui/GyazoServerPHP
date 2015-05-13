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


# run setup/*.sql > gyazo_hj
echo "--------------------------------------------------"
echo "run latest/*.sql > gyazo_hj"

files="./setup/*.sql"
for filepath in ${files}
do
    echo "mysql gyazo_hj < "${filepath}
    mysql gyazo_hj < ${filepath}
done

# run setup/*.sql > test_property
echo "--------------------------------------------------"
echo "run latest/*.sql > test_gyazo_hj"

files="./setup/*.sql"
for filepath in ${files}
do
    echo "mysql test_gyazo_hj < "${filepath}
    mysql test_gyazo_hj < ${filepath}
done

echo "done"
