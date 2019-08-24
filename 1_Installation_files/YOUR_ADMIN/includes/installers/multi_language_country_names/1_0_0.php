<?php

// use $configuration_group_id where needed
// For Admin Pages

$zc150 = (PROJECT_VERSION_MAJOR > 1 || (PROJECT_VERSION_MAJOR == 1 && substr(PROJECT_VERSION_MINOR, 0, 3) >= 5));
if ($zc150) { // continue Zen Cart 1.5.0
  $admin_page = 'configMultiLanguageCountryNames';
  // delete configuration menu
  $db->Execute("DELETE FROM " . TABLE_ADMIN_PAGES . " WHERE page_key = '" . $admin_page . "' LIMIT 1;");
  // add configuration menu
  if (!zen_page_key_exists($admin_page)) {
    if ((int)$configuration_group_id > 0) {
      zen_register_admin_page($admin_page, 'BOX_MULTI_LANGUAGE_COUNTRY_NAMES', 'FILENAME_CONFIGURATION', 'gID=' . $configuration_group_id, 'configuration', 'Y', $configuration_group_id);

      $messageStack->add('Enabled Multi Language Country Names Configuration Menu.', 'success');
    }
  }
}

// Table structure for table `countries_name`
$db->Execute("CREATE TABLE IF NOT EXISTS " . TABLE_COUNTRIES_NAME . " (
  id int(11) NOT NULL AUTO_INCREMENT,
  countries_id int(11) NOT NULL,
  language_id int(11) NOT NULL DEFAULT '1',
  countries_name varchar(64) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE countries (countries_id, language_id, countries_name),
  KEY idx_countries_name_zen (countries_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$selectCountryNamesQuery = "SELECT countries_id, countries_name
                            FROM " . TABLE_COUNTRIES . "
                            ORDER BY countries_id ASC";

$languages = zen_get_languages();

for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
  $selectCountryNames = $db->Execute($selectCountryNamesQuery);
  $language_id = $languages[$i]['id'];
  foreach ($selectCountryNames as $selectCountryName) {
    $countryNameArray = array(
      'countries_id' => $selectCountryName['countries_id'],
      'language_id' => $language_id,
      'countries_name' => $selectCountryName['countries_name']);
    zen_db_perform(TABLE_COUNTRIES_NAME, $countryNameArray);
  }
}
