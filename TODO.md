## Things To Do

* Figure out how to make the __invoke method so that the first 
  2 params are $req, $res and the remaining is variable.
* Ensure we use the form ID to set/get
    * POST method data
    * GET method data
    * Session data
    * Twig data
  * Figure out how to pass the form the template as the view so that it can render its own key
* FormService::render checks to see if th key for the form was rendered, if not then throws an exception.
* Change FormService interface
* Add Validation::{greaterThan,lessThan,equal,notEqual}; Make them so they can take callbacks that take all input and can massage the data before compare.
* Add custom validator, which just uses a callback to validate a field. Which may help with refactoring code when migrating.
