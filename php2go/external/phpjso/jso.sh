#!/bin/bash

processDir() {
	currentUser=`id -un`;
	currentGroup=`id -gn`;
        for i in $1/*; do

		if [ $i = "$1/*" ]; then

			echo Empty dir $i;
			break;

		fi

		if [ ! $3 = "" ]; then

			res=`echo $i | egrep $3`;
			if [ ! $res = "" ]; then

				echo Skipping $i;
				continue;

			fi

		fi

                if [ -d $i ]; then

                        processDir $i $2 $3;

                else

               		res=`echo $i | egrep "\.js$"`;
                	if [ ! $res = "" ]; then			

                        	ret=`php $2/jso.php $i`;
				if [ $ret = "ERR" ]; then

					echo Error processing $i;
					exit;

				else

					chown $currentUser:$currentGroup $i;
					chmod 755 $i;
					echo $i processed OK

				fi

			else

				echo $i is not a JS file;

			fi

                fi

        done
}

if [ $# -lt 2 ]; then
	echo Wrong parameter count. Aborting...;
	exit;
fi

if [ $# -eq 3 ]; then
	skip=$3;
else
	skip="";
fi

if [ -d $1 ] && [ -d $2 ]; then
	
	baseSourceDir=$1;
	baseTargetDir=$2;
	scriptDir='/www/extensions/php2go/external/phpjso/';
	cd $baseSourceDir;
	cp -rf * $baseTargetDir;
	processDir $baseTargetDir $scriptDir $skip;

else

	echo Source and target dirs must be valid directories;
	exit;

fi
