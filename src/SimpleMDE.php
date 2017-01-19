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

use RunBB\Core\Interfaces\User;
use RunBB\Core\Plugin;
use RunBB\Core\Utils;

class SimpleMDE extends Plugin
{
    const NAME = 'simplemde';// config key name
    const TITLE = 'SimpleMDE Toolbar';
    const DESCRIPTION = 'Add a <a href="https://github.com/NextStepWebs/simplemde-markdown-editor">SimpleMDE</a>'.
    ' toolbar to markdown format text in your posts.';
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

        // Add language files into javascript footer block
        $this->c['hooks']->bind('view.alter_data', [$this, 'addJs']);
        // Support default actions
        $this->c['hooks']->bind('controller.post.create', [$this, 'addToolbar']);
        $this->c['hooks']->bind('controller.post.edit', [$this, 'addToolbar']);
        $this->c['hooks']->bind('controller.topic.display', [$this, 'addToolbar']);
        // Support PMs plugin
        $this->c['hooks']->bind('conversationsPlugin.send.preview', [$this, 'addToolbar']);
        $this->c['hooks']->bind('conversationsPlugin.send.display', [$this, 'addToolbar']);
        // Profile signature edit
        $this->c['hooks']->bind('controller.profile.display', [$this, 'addToolbar']);
        // Post Report (need wysiwyg ????)
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
                toolbar: [
                    "bold", "italic", "strikethrough", "|",
                    "heading", "|",
                    "code", "quote", "unordered-list", "ordered-list", "clean-block", "|",
                    "link", "image", "table", "horizontal-rule", "|",
                    "preview", "side-by-side", "fullscreen", "|",
                    "undo", "redo", "|",
                    {// example own button and action
                        name: "testBaton",
                        action: getSmile,
                        className: "fa fa-smile-o",
                        id: "smilieBaton",
                        title: "SmiliesBaton",
                    }
                ],
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


            function getSmile () {//req_message
                $("#simplemde").emoji({
                    button: "#smilieBaton",
                    showTab: false,// show emoji groups
                    animation: "slide",// "fade", "slide" or "none"
                    icons: [{
                        name: "Emoji", // Emoji name
                        path: "/assets/img/smilies/",// path to the emoji icons
                        maxNum: 2,
                        file: ".png",// file extension name
                        placeholder: ":{alias}:",
                        excludeNums: [], // exclude emoji icons
                        title: {}, // titles of emoji icons
                        alias: {
                            0: "smile",
                            1: "mad",
                            2: "lol"
                        },
                    }]
                });
            };


            function drawTextFunction(editor) {
                var cm = editor.codemirror;
                var output = "";
                var selectedText = cm.getSelection();
                var text = selectedText || "placeholder";
            
                output = "!!!" + text + "!!!";
                cm.replaceSelection(output);
            }
        });
       
        
        ';
        // maybe where used
        $data['jsRAW'] = isset($data['jsRAW']) ? $data['jsRAW'] . $emdJs : $emdJs;

        return $data;
    }

    public function addToolbar()
    {
        //$args = func_get_args();
        View::addAsset(
            'css',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME . '/simplemde.min.css',
            ['type' => 'text/css', 'rel' => 'stylesheet']
        );
        View::addAsset(
            'css',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME . '/jquery.emoji.css',
            ['type' => 'text/css', 'rel' => 'stylesheet']
        );
        View::addAsset(
            'js',
//            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/simplemde.min.js',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/simplemde.js',
            ['type' => 'text/javascript']
        );
        View::addAsset(
            'jsTop',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/jquery.mCustomScrollbar.min.js',
            ['type' => 'text/javascript']
        );
        View::addAsset(
            'jsTop',
//            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/jquery.emoji.min.js',
            $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME .'/jquery.emoji.js',
            ['type' => 'text/javascript']
        );

        return true;
    }

    public function install()
    {
//        $to = $this->c['forum_env']['WEB_ROOT'] . $this->c['forum_env']['WEB_PLUGINS'].'/'.self::NAME;
//        $from = __DIR__ . '/assets';
        Utils::recurseCopy(
//            $from, $to
            __DIR__ . '/assets',
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