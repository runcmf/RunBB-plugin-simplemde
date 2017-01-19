# Markdown SimpleMDE editor for RunBB forum plugin


## Install
1.
```php
$ composer require runcmf/runbb-plugin-simplemde:dev-master
```

2. add to setting.php into `pugins` section `'simplemde' => 'SimpleMDE\SimpleMDE'`  
  like:
```php
        'plugins' => [// register plugins as NameSpace\InitInfoClass
                'simplemde' => 'SimpleMDE\SimpleMDE'
            ],
```
3. go to Administration -> Plugins -> SimpleMDE Toolbar -> Activate  


## Recommendations

* TODO


---
## Tests (TODO)
```bash
$ cd vendor/runcmf/runbb
$ composer update
$ vendor/bin/phpunit
```
---  
## Security  

If you discover any security related issues, please email to 1f7.wizard( at )gmail.com instead of using the issue tracker.  

---
## Credits
[SimpleMDE](https://github.com/NextStepWebs/simplemde-markdown-editor)  
[jQuery-emoji](https://github.com/eshengsky/jQuery-emoji)  


---
## License
 
```
Copyright 2016 1f7.wizard@gmail.com

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

