**Note for Portal**

- User roles use Profile Builder plugin to manage the roles
- Some pages have restrictions to some user roles (Check page edit screen)
- The sidebar menu use classes to hide the menu items, eg: `user-hidden--contractor`, `user-hidden--retail`, `user-hidden--installer` (check _general.scss)
- Some admin menu use CSS to hide (for the user roles that have READ capability), check `admin_and_public_head` and `remove_menus` functions in `class-por-admin.php` for more details
