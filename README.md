# Meetup Event Publisher

Retrieve data from a custom API for Meetup.com and publish it as blog post on your website.

## Requirements

* PHP 8.3
* WordPress 6.7

## Usage

After installing, go to **Settings > Writing > Meetup.com Slug** and set the slug of your meetup. The slug can be found in the URL of your meetup. E.g. the slug of https://www.meetup.com/wpmeetup-stuttgart/ is `wpmeetup-stuttgart`. If you leave the slug empty, all events from all meetups are retrieved.

After that, once daily the plugin checks the Meetup.com API for meetups and creates/updates the next meetup as blog post.

### Shortcode

You can access and output many data of the event, e.g. `name`, `local_date`, `local_time`, `link`, `description` etc.

Shortcode name: `[meetup_event]`

#### Attributes

`event`: Access a specific event. Allows selecting by the event name, the local date, the ID or the special command `next` for the next event. Example: `[meetup_event event="2025-10-27"]`

`exclude_protocol`: For the field `link`, this option, if set to `yes` allows to remove the `https://` from the link to allow using the shortcode in areas where `esc_url` is used (e.g. in button blocks). Example: `[meetup_event field=link exclude_protocol=yes]`

`field`: Select the specific field to output. Example: `[meetup_event field="name"]`<br>
If a field is nested, use dots to traverse the nesting. Example: `[meetup_event field="location.address.street_address"]`

`slug`: If no slug is configured in the settings, you can select the meetup slug of which you want to display data from.

## Building

First, you need to install all NPM dependencies. Make sure you have NPM installed first. Then, run this command:

```bash
npm install
```

### Development build

To build assets for development purposes and to watch for changes, run this command:

```bash
npm start
```

### Production build

To build assets for production, run this command:

```bash
npm run build
