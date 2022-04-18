{ pkgs }: {
	deps = [
    pkgs.php80
    pkgs.mariadb
    pkgs.php80Packages.composer
	];
}