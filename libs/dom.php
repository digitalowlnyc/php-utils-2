<?php
/**
 * Created by PhpStorm.
 * User: Bryan Mayor
 * Date: 4/30/16
 * Time: 7:11 PM
 */
function createDomDocumentFromUrl($url)
{
    $htmlAsString = HttpClient::create()->get($url)->check()->content();

    if($htmlAsString === false || strlen($htmlAsString) === 0) {
        die("Could not load: $url");
    }

    $dom = new DOMDocument;
    @$dom->loadHTML($htmlAsString);
    return [
        "document" => $dom,
        "html" => $htmlAsString
    ];
}

function getDomDocumentForUrl($url) {
    return createDomDocumentFromUrl($url)['document'];
}

/**
 * @param $dom
 * Reference:
 * http://php.net/manual/en/class.domnode.php
 * http://php.net/manual/en/book.dom.php
 * http://php.net/manual/en/domdocument.getelementsbytagname.php
 * http://php.net/manual/en/class.domnode.php#domnode.props.nodetype
 * http://php.net/manual/en/domelement.getattribute.php
 *
 * See also:
 * getElementById
 * getElementsByTagName
 */
function iterateDomInternal($dom, &$resultsArray, $selectionFunction = null, $recursive = true, $verbose = false, $depth = 0) {

    if(!$dom->hasChildNodes()) {
        return;
    }

    $children = $dom->childNodes;

    $childrenArray = array();

    foreach($children as $childNode) {
        $childrenArray[] = $childNode;

        $nodeDetails = describeNode($childNode);

        if(is_callable($selectionFunction) && $selectionFunction($nodeDetails)) {
            $resultsArray[] = $childNode;

            if($verbose) {
                echo str_repeat("-", $depth) . $nodeDetails["label"] . "<br>";
            }
        }

        if($recursive) {
            iterateDomInternal($childNode, $resultsArray, $selectionFunction, $recursive, $verbose, $depth++);
        }
    }
}

/**
 * Convert from DomNodeList to array
 * @param $dom
 * @return array
 */
function getChildNodes($dom) {
    if(!$dom->hasChildNodes()) {
        return [];
    }
    $childArray = [];
    $children = $dom->childNodes;
    foreach($children as $child) {
        $childArray[] = $child;
    }
    return $childArray;
}

function describeNode($domNode) {
    $nodeDetails = [
        "type" => $domNode->nodeType,
        "tag" => $domNode->nodeName,
        //"value" => $childNode-nodeValue
    ];

    if($domNode->nodeType === XML_ELEMENT_NODE) {
        if($domNode->nodeName === "script") {
            $label = describeScriptNode($domNode);
        } else if($domNode->nodeName === "a") {
            $label = describeAnchorNode($domNode);
        } else {
            if($domNode->hasAttribute("id"))
                $nodeDetails["id"] = $domNode->getAttribute("id");
            if($domNode->hasAttribute("class"))
                $nodeDetails["class"] = $domNode->getAttribute("class");

            $label = $domNode->nodeName;

            if($domNode->hasAttribute("id")) {
                $label = $label . "#" . $domNode->getAttribute("id");
            } else if($domNode->hasAttribute("class")) {
                $label = $label . "." . $domNode->getAttribute("class");
            }
        }
        $nodeDetails["label"] = $label;
    }


    return $nodeDetails;
}

function describeAnchorNode($anchorNode) {
    if($anchorNode->nodeType !== 1 || $anchorNode->nodeName != "a") {
        throw new Exception("Node must be anchor element");
    }

    $label = $anchorNode->nodeName;
    if($anchorNode->hasAttribute("href")) {
        $label = $label . " "  . $anchorNode->getAttribute("href");
    }
    return $label;
}


function describeScriptNode($scriptNode) {
    if($scriptNode->nodeType !== 1 || $scriptNode->nodeName != "script") {
        throw new Exception("Node must be script element");
    }

    $label = $scriptNode->nodeName;
    if($scriptNode->hasAttribute("type")) {
        $label = $label . " "  . $scriptNode->getAttribute("type");
    }
    if($scriptNode->hasAttribute("src")) {
        $label = $label . " "  . $scriptNode->getAttribute("src");
    }
    return $label;
}

function findDomSibilingNodes($domNode, $selectors) {
    return findDomNodes($domNode->parentNode, $selectors, false);
}

function findDomNodes($domNode, $selectors, $recursive = true) {

    $tag = isset($selectors["tag"]) ? $selectors["tag"] : null;
    $cssClass = isset($selectors["class"]) ? $selectors["class"] : null;
    $id = isset($selectors["id"]) ? $selectors["id"] : null;

    if($tag === null && $cssClass === null &&  $id === null) {
        throw new Exception("Must provide tag, class or id");
    }

    $selectionFunction = function($nodeDetailsArray) use ($tag, $cssClass, $id) {
        if($tag === null || (isset($nodeDetailsArray["tag"]) && $nodeDetailsArray["tag"] === $tag)) {
            if($id === null || (isset($nodeDetailsArray["id"]) && $nodeDetailsArray["id"] === $id)) {
                if($cssClass === null || (isset($nodeDetailsArray["class"]) && $nodeDetailsArray["class"] === $cssClass)) {
                    return true;
                }
            }
        }
        return false;
    };

    return iterateDom($domNode, $selectionFunction, $recursive);
}

function printDom($dom) {

    $selectElementNodes = function($domNodeDetails) {
        return $domNodeDetails["type"] == 1;
    };

    iterateDom($dom, $selectElementNodes, true, true);
}

function iterateDom($dom, $selectionFunction = null, $recursive = true, $verbose = false) {
    $results = [];
    iterateDomInternal($dom, $results, $selectionFunction, $recursive, $verbose);
    return $results;
}

/**
 * List external assets - $stylesheets, scripts, icons
 * @param $domDocument
 * @return array
 */
function domListAssets($domDocument) {
    $stylesheets = [];
    $icons = [];
    $scripts = [];
    $images = [];

    foreach ($domDocument->getElementsByTagName('link') as $linkEl){
        $rel = $linkEl->getAttribute('rel');
        $href = $linkEl->getAttribute('href');

        if(strlen($href) === 0) {
            println("Not including: " . $rel);
            continue;
        }

        if($rel == "stylesheet") {
            $stylesheets[] = $href;
        } else if($rel == "icon") {
            $icons[] = $href;
        }
    }

    foreach ($domDocument->getElementsByTagName('script') as $scriptEl){
        $scriptSrc = $scriptEl->getAttribute('src');

        if(strlen($scriptSrc) === 0) {
            continue;
        }
        $scripts[] = $scriptSrc;
    }

    foreach ($domDocument->getElementsByTagName('img') as $imgElement){
        $imageSrc = $imgElement->getAttribute('src');

        if(strlen($imageSrc) === 0) {
            continue;
        }
        $images[] = $imageSrc;
    }

    return [
        "stylesheets" => $stylesheets,
        "scripts" => $scripts,
        "icons" => $icons,
        "images" => $images
    ];
}