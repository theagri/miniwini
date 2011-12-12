#!/usr/bin/env ruby
stamp = Time.now.to_s

targets = {
  "desktop" => [
    'common/reset.css',
    'common/base.css',
    'common/ui.css',
    'common/commently.css',
    
    'desktop/fonts.css',
    'desktop/layout.css',
    'desktop/board.css',

    ],
}

targets.each{|target, files|

  result = "/* generated: " + stamp + " */\n"
  files.each{|css|
      f = File.new("public/css/" + css, "r")
        result << f.read
      f.close
  }

  File.open("public/css/" + target + ".css", 'w') {|f| f.write(result) }
}



