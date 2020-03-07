<?php 
	function ananToArticle() {
		$json = file_get_contents("article_contents.json");
		$data = json_decode($json,true);
		
		foreach($data as $el) {
			$initials = array('/', ' ');
			$transformed = array('-', 'T');
			//配列作成
			$array = [
				'id' => "urn:ngsi-ld:Article:p41m697-".str_replace(".html", "", basename($el["url"])),
				'type' => 'Article',
				'original_url' => [
					'type' => 'Property',
					'value' => $el["url"]
				],
				'title' => [
					'type' => 'Property',
					'value' => $el["title"],
					'num_title_text' => mb_strlen($el["title"])
				],
				'register_date' => [
					'type' => 'Property',
					'value' => str_replace($initials, $transformed, $el["register_date"]).'+0900'
				],
				'main_text' => [
					'type' => 'Property',
					'value' => $el["main_text"],
					'num_main_text' => mb_strlen($el["main_text"])
				]
			];
			
			echo json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
	}
	
	function ananTitleToNamedEntity() {
		$json = file_get_contents("article_contents.json");
		$data = json_decode($json,true);
		
		foreach($data as $el) {
			//MeCab
			$mecab = new \MeCab\Tagger();
			
			$titleText = explode("\n", $mecab->parse($el["title"]));
			array_pop($titleText);
			//print_r($titleText); exit;
			
			$count1 = 0;
			$wstart = 0;
			$wend = 0;
			$wlength = 0;
			$el2 = array();
			
			foreach($titleText as $valtitle) {
				$el2 = explode(',', $valtitle);
				$wlength = mb_strlen($el2[0]);
				$wend = $wstart + $wlength - 1;
				
				//neクラス
				if (mb_strpos(end($el2), 'ANAN_') !== false) {
					$array = [
						'id' => "urn:ngsi-ld:NamedEntity:p41m697-".str_replace(".html", "", basename($el["url"]))."-title-".$count1,
						'type' => 'NamedEntity',
						'ne' => [
							'type' => 'Property',
							'value' => $el2[0],
							'class' => str_replace("ANAN_", "", end($el2)),
							'section_ne' => 'title',
							'word_loc' => [
								'word_start' => $wstart,
								'word_end' => $wend
							]
						],
						'refArticle' => [
							'type' => 'Relationship',
							'object' => 'urn:ngsi-ld:Article:p41m697-'.str_replace(".html", "", basename($el["url"]))
						],
						'refNamedEntityClass' => [
							'type' => 'Relationship',
							'object' => 'urn:ngsi-ld:NamedEntityClass:'.str_replace("ANAN_", "", end($el2))
						]
					];
					echo json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
					$count1++;
				}
				
				$wstart = $wend + 1;
			}	
		}
	}

	function ananMaintextToNamedEntity() {
		$json = file_get_contents("article_contents.json");
		$data = json_decode($json,true);
		
		foreach($data as $el) {
			//MeCab
			$mecab = new \MeCab\Tagger();
			
			$mainText = explode("\n", $mecab->parse($el["main_text"]));
			array_pop($mainText);
			#print_r($mainText); exit;
			
			$count1 = 0;
			$wstart = 0;
			$wend = 0;
			$wlength = 0;
			$el2 = array();
			
			foreach($mainText as $valtitle) {
				$el2 = explode(',', $valtitle);
				$wlength = mb_strlen($el2[0]);
				$wend = $wstart + $wlength - 1;
				
				//neクラス
				if (mb_strpos(end($el2), 'ANAN_') !== false &&
				mb_strpos(end($el2), 'ANAN_ETC') === false) {
					$array = [
						'id' => "urn:ngsi-ld:NamedEntity:p41m697-".str_replace(".html", "", basename($el["url"]))."-main-".$count1,
						'type' => 'NamedEntity',
						'ne' => [
							'type' => 'Property',
							'value' => $el2[0],
							'class' => str_replace("ANAN_", "", end($el2)),
							'section_ne' => 'main_text',
							'word_loc' => [
								'word_start' => $wstart,
								'word_end' => $wend
							]
						],
						'refArticle' => [
							'type' => 'Relationship',
							'object' => 'urn:ngsi-ld:Article:p41m697-'.str_replace(".html", "", basename($el["url"]))
						],
						'refNamedEntityClass' => [
							'type' => 'Relationship',
							'object' => 'urn:ngsi-ld:NamedEntityClass:'.str_replace("ANAN_", "", end($el2))
						]
					];
					echo json_encode($array, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
					$count1++;
				}
				
				$wstart = $wend + 1;
			}	
		}
	}

	//実行
	ananToArticle();
	//ananTitleToNamedEntity();
	//ananMaintextToNamedEntity();
?>