### WordPress shortcode popups generator.
WordPress shortcode popups generator is a PHP class, which facilitates creation of WordPress dynamic popup windows. This should help the developers out in creation of a well thught user interface for a more complex shortcodes having several fields. Generally manual management of shortcodes is quite cumbersome, our class aims to sort that issue out.
(for a more visually appealing version of the readme, please see [github pages](http://gicolek.github.io/shortcode-popups/))

### Basic Usage

1. First instantiate the class

`$shortcodes = new WP_Doin_Shortcodes_Generator();`

2. Add a shortcode

`$test = $shortcodes->add_shortcode( 'acfrpw', 'ACFRPW', 'Choose from a list of settings to create the acfrpw shortocde. Any content (shortcodes as well) can be placed in the textareas.' );`

3. Add some fields

`$test->add_field( 'text', 'css', __( 'CSS Class', 'acf_rpw' ) );`

4. Generate the shortcode

`$shortcodes->generate();`

You'll end up with a popup window that would place the following shortcode for you:

`[acfrpw css="the text you specified"]`

### Available fields

Currently there are 5 available fields to specify. The second argument of each add_field method needs to be different, as the values are stored as keys of the associative array. All of the fields take from 3 to 5 arguments, the first one denotes the type of the field used, the second one, a uniqe name of the field. The third one a heading of the element, which should describe the element, the fourth one a description, which describes the element in detail and the fift one, used for the select and checkbox fields is an array of key -> value pair used for the values and names of select options and checkbox fields.

* Column field 
The column field is used to divide the viewport to 1/3. Each column should have a 'start' and 'end' elements, placed respectively before and after all other column fields

`->add_field( 'col', 'fcs', 'start' )`

* Text field

The text field takes 3 to 4 arguments, the first one denotes the type of the field (text), the second one its unique name, the third one its heading and the foruth one an optional description.

`->add_field( 'text', 'css', __( 'CSS Class', 'acf_rpw' ) )`

* Select field

The select field takes 5 arguments, the first one denotes the type of the field (select), the second one its unique name, the third one its heading, the fourth one a description (input empty string for none) and the fifth its key -> value pairs.

`>add_field( 'select', 'ord', __( 'Order', 'acf_rpw' ), '', array( 'ASC' => __( 'Ascending', 'acf_rpw' ), 'DESC' => __( 'Descending', 'acf_rpw' ) ) )` 

See the source code for more information.

