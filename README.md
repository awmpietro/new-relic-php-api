How to use:

1 - List applications associated with one or many api keys:
$newrelic = new Newrlic();
//array of apykey(s)
$apikeys = array('apikey1', 'apíkey2', ...);
//Returns a nested json with all applications where the json key is the apikey.
$appications = $newrelic->getList($apikeys);

1.2 - Filter applications - You can filter applications from an apikey by it´s name, id or language
$newrelic = new Newrlic();
//array of single apikey
$apikeys = array('apikey1');
//Filter by name (one single)
$newrelic->setName('applicationName');
//Filter by ids(single or many). One string separated by comma
$newrelic->setIds('id1,id2,id3');
//Filter by language (single)
$newrelic->setLanguage('java');
//And then call the getList() method again. Returns a json with the desired application.
$appications = $newrelic->getList($apikeys);

2 - List a single application from an apikey
$newrelic = new Newrlic();
//pass the apikey and the application id as parameters
$application = $newrelic->getShow($apikey, $id)

3 - Update an application - You can update certain parameters of an application: name (string), app_apdex_threshold(float), end_user_apdex_threshold(float), enable_real_user_monitoring(boolean)
$newrelic = new Newrlic();
//create an array of the data to be updated. Example: $data = array('name' => 'newname', 'app_apdex_threshold' => 'new float value', 'end_user_apdex_threshold' => 'new float value', 'enable_real_user_monitoring' => 'true or false');
//pass the apikey. the application id and the $data array as parameters to update function
$updatedApplication = $newrelic->update($apikey, $id, $data)
//returns a json of the updated application

4 - Delete an application - You can delete an application by it´s apikey and id.
//WARNING: Cannot delete an application that is still reporting data, and if deletes it´s irreversible
$newrelic = new Newrlic();
$newrelic->delete($apikey, $id)
