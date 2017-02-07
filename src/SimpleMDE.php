<?php
/**
 * Copyright 2017 1f7.wizard@gmail.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace SimpleMDE;

use RunBB\Core\Plugin;
use RunBB\Core\Utils;

class SimpleMDE extends Plugin
{
    const NAME = 'simplemde';// config key name
    const TITLE = 'SimpleMDE Toolbar';
    const DESCRIPTION = '<a href="https://github.com/NextStepWebs/simplemde-markdown-editor">SimpleMDE</a>'.
    ' markdown editor panel.';
    const VERSION = '0.1.0';
    const KEYWORDS = [
        'runbb',
        'markdown',
        'wysiwyg',
        'SimpleMDE',
        'toolbar',
        'helper',
        'messages'
    ];
    const AUTHOR = [
        'name' => '1f7'
    ];

    /**
     * Back compatibility with featherBB plugins
     *
     * @return string
     */
    public static function getInfo()
    {
        $cfg = [//TODO rebuild use composer.json
            'name' => self::NAME,// config key name
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'version' => self::VERSION,
            'keywords' => self::KEYWORDS,
            'author' => self::AUTHOR
        ];
        return json_encode($cfg);
    }

    public function run()
    {
        Statical::addNamespace('*', __NAMESPACE__.'\\*');

        // Support default actions
        $this->c['hooks']->bind('controller.post.create', [$this, 'addToolbar']);
        $this->c['hooks']->bind('controller.post.edit', [$this, 'addToolbar']);
        $this->c['hooks']->bind('controller.topic.display', [$this, 'addToolbar']);
        // Support PMs plugin
        $this->c['hooks']->bind('conversationsPlugin.send.preview', [$this, 'addToolbar']);
        $this->c['hooks']->bind('conversationsPlugin.send.display', [$this, 'addToolbar']);
        // Profile signature edit
        $this->c['hooks']->bind('controller.profile.display', [$this, 'addToolbar']);
        // Post Report
        $this->c['hooks']->bind('controller.post.report', [$this, 'addToolbar']);
    }

    /**
     * Hook method must be public
     * @param $data
     * @return mixed
     */
    public function addJs($data)
    {
        // TODO build editor depend user rights and config
        $emdJs = '
        document.addEventListener("DOMContentLoaded", function(event) {
            var simplemde = new SimpleMDE({ 
                element: $("#req_message")[0],
//                toolbar: [
//                    "bold", "italic", "strikethrough", "|", "heading", "|",
//                    "code", "quote", "unordered-list", "ordered-list", "clean-block", "|",
//                    "link", "image", "table", "horizontal-rule", "|",
//                    "preview", "side-by-side", "fullscreen", "|", "undo", "redo"
//                ],
                showIcons: ["strikethrough", "code", "clean-block", "table", "horizontal-rule"],
                hideIcons: ["guide", "fullscreen"],
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                spellChecker: false,
                autoDownloadFontAwesome: false,
                forceSync: true,
                insertTexts: {
                    horizontalRule: ["", "\n\n-----\n\n"],
                    image: ["![](http://", ")"],
                    link: ["[", "](http://)"],
                    table: ["", "\n\n| Column 1 | Column 2 | Column 3 |\n" + 
                    "| -------- | -------- | -------- |\n| Text     | Text      | Text     |\n\n"],
                },
                status: ["autosave", "lines", "words", "cursor", {
                    className: "keystrokes",
                    defaultValue: function(el) {
                        this.keystrokes = 0;
                        el.innerHTML = "0 Keystrokes";
                    },
                    onUpdate: function(el) {
                        el.innerHTML = ++this.keystrokes + " Keystrokes";
                    }
                }]
            });
        });
        ';
        // maybe where used
        $data['jsraw'] = isset($data['jsraw']) ? $data['jsraw'] . $emdJs : $emdJs;

        return $data;
    }

    public function addToolbar()
    {
        // Add language files into javascript footer block
        $this->c['hooks']->bind('view.alter_data', [$this, 'addJs']);

        //$args = func_get_args();
        View::addAsset(
            'css',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME . '/simplemde.min.css',
            ['type' => 'text/css', 'rel' => 'stylesheet']
        );
        View::addAsset(
            'js',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/simplemde.min.js',
            ['type' => 'text/javascript']
        );

        return true;
    }

    public function install()
    {
        Utils::recurseCopy(
            realpath(__DIR__ . '/../assets'),
            $this->c['forum_env']['WEB_ROOT'] . $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME
        );
    }

    public function remove()
    {
        // TODO
        //Utils::recurseDelete($dir);
    }

    public function update()
    {
        // TODO
    }
}