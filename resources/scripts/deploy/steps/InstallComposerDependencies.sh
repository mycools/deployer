### Install composer dependencies - {{ deployment }}
checkcomposer() {
	# Check composer is installed
    if type -P composer &> /dev/null; then
        composer="$(command -v composer)"
    else
        # If not, check for composer.phar
        if type -P composer.phar &> /dev/null; then
            composer="$(command -v composer.phar)"
        else
            # If still not, check for composer.phar in the project path
            if [ ! -f {{ project_path }}/composer.phar ]; then
                # Finally, resort to downloading it
                curl -sS https://getcomposer.org/installer | php
                chmod +x composer.phar
            fi

            composer="php {{ project_path }}/composer.phar"
        fi
    fi

   
}
installpackage() {

    cd {{ release_path }}

    # Check if the --no-suggest flag exists, from composer >= 1.2
    suggest=""
    if [ $(${composer} help install | grep 'no-suggest' | wc -l) -gt 0 ]; then
         suggest="--no-suggest"
    fi

    if [ -n "{{ include_dev }}" ]; then

        ${composer} install --no-interaction --optimize-autoloader \
                          --prefer-dist ${suggest} --no-ansi --working-dir {{ release_path }}
    else
        ${composer} install --no-interaction --optimize-autoloader \
                          --no-dev --prefer-dist ${suggest} --no-ansi --working-dir {{ release_path }}
    fi
}
cd {{ project_path }}

# If there is no composer file, skip this step
if [ -f {{ release_path }}/composer.json ]; then
	checkcomposer
	if [ -f {{ project_path }}/latest/composer.json ]; then
		hash1=`md5sum {{ release_path }}/composer.json | awk '{print $1}'`
		hash2=`md5sum {{ project_path }}/latest/composer.json | awk '{print $1}'`
		if [ "$hash1" = "$hash2" ]
		then
		  echo "composer.json are the same."
		  cp -R {{ project_path }}/latest/vendor /{{ release_path }}/vendor
		  cd {{ release_path }}
		  ${composer} dump-autoload
		else
		  echo "composer.json are different ($hash1)($hash2)."
		  installpackage
		fi
	else
		installpackage
    
	fi
	
    
fi

cd {{ release_path }}
