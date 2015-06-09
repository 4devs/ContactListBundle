Getting Started With ContactListBundle
======================================

## Installation and usage

Installation and usage is a quick:

1. Download ContactListBundle using composer
2. Enable the Bundle
3. Create file contact
4. Use Doctrine [MongoDB](mongodb.org)
5. Use [SonataAdminBundle](https://sonata-project.org/bundles/admin/2-3/doc/index.html)

### Step 1: Download ContactListBundle using composer

Add ContactListBundle in your composer.json:

```js
{
    "require": {
        "fdevs/contact-list-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update fdevs/contact-list-bundle
```

Composer will install the bundle to your project's `vendor/fdevs` directory.


### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FDevs\ContactListBundle\FDevsContactListBundle(),
    );
}
```

base config

``` yaml
# app/config/config.yml
f_devs_contact_list:
    providers:
        - f_devs_contact_list.contact_provider.builder_alias
    db:
        driver: mongodb # 'none' by default
        manager_name: null
    admin_service: sonata # 'none' by default  
    tpl: fdevs_contact.html.twig
    model_contact: FDevs\ContactList\Model\Contact
    model_connect: FDevs\ContactList\Model\Connect
    model_address: FDevs\ContactList\Model\Address
```



### Step 3: Create file contact

add config provider

``` yaml
# app/config/config.yml
f_devs_contact_list:
    providers:
        - f_devs_contact_list.contact_provider.builder_alias
```

create contact

```php
<?php
//src/AppBundle/Contact/Footer.php

namespace AppBundle\Contact;

use FDevs\ContactList\FactoryInterface;

class Footer
{
    public function social(FactoryInterface $factory, array $options = [])
    {
        $contact = $factory->createContact('social');
        $factory->addAddress($contact, 'Россия', 'Москва', 'Москва', '', '103132', 'ул. Ильинка, д. 23', 'ru');
        $factory->addAddress($contact, 'Russia', 'Moscow', 'Moscow', '', '103132', 'st. Ilinka Str. 23', 'en');

        $factory->addConnect($contact, 'skype', 'andrey', 'andrey');
        $factory->addConnect($contact, 'email', 'andrey', 'andrey@4devs.org');
        $factory->addConnect($contact, 'phone', 'Телефон', '88002002316');
        $factory->addConnect($contact, 'fax', '+7(095)206-07-66', '+70952060766');
        $factory->addConnect($contact, 'github', '4devs', 'https://github.com/4devs');

        return $contact;
    }
}

```

in your template

``` twig
{{ contact('AppBundle:Footer:social') }}
```


### Step 4: Use MongoDB

add config

``` yaml
# app/config/config.yml
f_devs_contact_list:
    providers:
        - f_devs_contact_list.contact_provider.doctrine
    db:
        driver: mongodb # 'none' by default
        manager_name: null
```

### Step 4: Use SonataAdminBundle

add config 

``` yaml
# app/config/config.yml
f_devs_contact_list:
    admin_service: sonata # 'none' by default  

sonata_admin:
    dashboard:
        groups:
            label.contactUs:
                label_catalogue: FDevsContactListBundle
                items:
                    - f_devs_contact_list.admin.contact
```

### Step 5: Add/replace Template


add config 

``` yaml
# app/config/config.yml
f_devs_contact_list:
    tpl: AppBundle:Contact:contact.html.twig
```

create template

```twig
{#src/AppBundle/Resources/views/Contact/contact.html.twig#}
{% extends 'fdevs_contact.html.twig' %}

{% block fdevs_address_name %}
{% endblock fdevs_address_name %}

{% block fdevs_connect %}
<li>
    <i class="fa fa-{{ data.type }}"></i>
    {{ data.type|trans({},'AppBundle')|title }}:
    <a href="{{ data.href }}" title="{{ data.text }}">{{ data.href }}</a>
</li>
{% endblock fdevs_connect %}

{% block envelope_connect %}
<li>
    <i class="fa fa-envelope-o"></i>
    {{ 'email'|trans({},'AppBundle')|title }}:
    <a href="mailto:{{ data.href }}" title="{{ data.text }}" itemprop="email">{{ data.href }}</a>
</li>
{% endblock envelope_connect %}

{% block phone_connect %}
<li>
    <i class="fa fa-phone"></i>
    {{ 'phone'|trans({},'AppBundle')|title }}:
    <a href="tel:{{ data.href }}" title="{{ data.text }}" itemprop="telephone">{{ data.text }}</a>
</li>
{% endblock phone_connect %}

{% block skype_connect %}
<li>
    <i class="fa fa-skype"></i> {{ 'skype'|trans({},'AppBundle')|title }}:
    <a href="skype:{{ data.href }}" title="{{ data.text }}">{{ data.href }}</a>
</li>
{% endblock skype_connect %}

{% block footer_connect %}
<li>
    <a href="{{ data.href }}" title="{{ data.text }}" class="btn-social btn-outline">
        <i class="fa fa-fw fa-{{ data.type }}"></i>
    </a>
</li>
{% endblock footer_connect %}

{% block contact_us_contact %}
{% spaceless %}
    <ul class="list-inline" itemscope itemtype="http://schema.org/Organization">

        <meta itemprop="name" content="{{ 'title'|trans({},'AppBundle') }}"/>
        <meta itemprop="description"
              content="{{ 'meta.description'|trans({},'AppBundle') ~ ' ' ~ 'about.subheading'|trans({},'AppBundle') ~ ' ' ~ 'portfolio.subheading'|trans({},'AppBundle') }}"/>
        {{ address(contact.address,options.prefix,options.locale) }}
        <ul class="list-inline social-buttons">
            {% for oneConnect in contact.connectList %}
                {{ connect(oneConnect,options.prefix) }}
            {% endfor %}
        </ul>
        </div>
    </ul>
{% endspaceless %}
{% endblock contact_us_contact %}

{% block contact_address %}
{% spaceless %}
    <li itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
        <i class="fa fa-map-marker"></i> {{ 'address'|trans({},'AppBundle') }}:
        {% if address.street %}
            <span itemprop="streetAddress" class="postal-address__street">{{ address.street }}</span>
        {% endif %}
        {% if address.code %}
            <span itemprop="postalCode" class="postal-address__code">{{ address.code }}</span>
        {% endif %}
        {% if address.box %}
            <span itemprop="postOfficeBoxNumber" class="postal-address__box">{{ address.box }}</span>
        {% endif %}
        {% if address.region %}
            <span itemprop="addressRegion" class="postal-address__region">{{ address.region }}</span>
        {% endif %}
        {% if address.locality %}
            <span itemprop="addressLocality" class="postal-address__locality">{{ address.locality }}</span>
        {% endif %}
        {% if address.country %}
            <span itemprop="addressCountry" class="postal-address__country">{{ address.country }}</span>
        {% endif %}
    </li>
{% endspaceless %}
{% endblock contact_address %}
```

add template

``` twig
{{ contact('AppBundle:Footer:social','footer') }}
```

#### template blocks priority

* contact block name by priority
** `{prefix}_contact`
** `{slug}_contact`
** `fdevs_contact`

* address block name by priority
** `{prefix}_{locale}_address`
** `{prefix}_address`
** `{locale}_address`
** `fdevs_{locale}_address`
** `fdevs_address`

* connect block name by priority
** `{prefix}_{type}_connect`
** `{type}_connect`
** `{contact_prefix|slug}_{type}_connect` - if use as contact  
** `{contact_prefix|slug}_connect` - if use as contact  
** `fdevs_connect`
