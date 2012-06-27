#!/usr/bin/perl

sub trim($)
{
	my $string = shift;
	$string =~ s/^\s+//;
	$string =~ s/\s+$//;
	return $string;
}

sub write_data() 
{
 my @queries = @{shift};
 	
}

use Switch;
@reserves;
@catalog;
@summon;
@blend;

while(<>) {
  $log_entry = $_;
  if($log_entry =~ /QUERY:(.+)REDIRECT/) {
    switch($log_entry) {
      case /TAB:location/ { push(@catalog, trim('"' . $1 . '"')); }
      case /TAB:summon/ { push(@summon, trim('"' . $1 . '"')); }
      case /TAB:course/ { push(@reserves, trim('"' . $1 . '"')); }
      case /TAB:blended/ { push(@blend, trim('"' . $1 . '"')); }
      else {}
    }
  }
}


# sorting of arrays
@cat = sort {lc $a cmp lc $b} @catalog;
@sum = sort {lc $a cmp lc $b} @summon;
@res = sort {lc $a cmp lc $b} @reserves;
@ble = sort {lc $a cmp lc $b} @blend;

$cat_log = "./raw/catalog.csv";
$sum_log = "./raw/summon.csv";
$res_log = "./raw/reserves.csv";
$ble_log = "./raw/blended.csv";


open FH, ">>$cat_log" or die "can't open '$cat_log': $!";
foreach(@cat) {
  print FH trim($_) . "\n";
}
close FH;

open FH, ">>$sum_log" or die "can't open '$sum_log': $!";
foreach(@sum) {
  print FH trim($_) . "\n";
}
close FH;

open FH, ">>$res_log" or die "can't open '$res_log': $!";
foreach(@res) {
  print FH trim($_) . "\n";
}
close FH;

open FH, ">>$ble_log" or die "can't open '$ble_log': $!";
foreach(@ble) {
  print FH trim($_) . "\n";
}
close FH;
  

print "Catalog:" . @catalog . "\n";
print "Summon:" . @summon . "\n";
print "Reserves:" . @reserves . "\n";
print "Blended:" . @blend . "\n";
$totals = @catalog + @summon + @reserves + @blend;
print "Totals:" . $totals . "\n";
