import scrapy
import time

class MenuSpider(scrapy.Spider):
    name = "menu"
    page_number = 2

    start_urls = ["https://starbuckssecretmenu.net/category/secret-frappuccinos/"]

    def parse(self, response):
        for row in response.css(".td-block-span6"):
            time.sleep(1)
            #ret = {}
            #name = row.css("img::attr(title)").extract_first()
            #preview = row.css("img::attr(src)").extract_first()
            detail_page = row.css("a::attr(href)").extract_first()
            url = response.urljoin(detail_page)
            yield scrapy.Request(url, callback = self.parse_detail)

            #if "|" in name:
            #    ret["name"] = name[:name.index("|")-1]
            #elif ":" in name:
            #    ret["name"] = name[name.index(":")+2:]
            #else:
            #    ret["name"] = name

            #ret["preview"] = preview
            
            #if detail_page is not None:
            #    ret["detail"] = yield response.follow(detail_page, callback=self.parse_detail)
            
            #yield ret

        #next_page = "https://starbuckssecretmenu.net/category/secret-frappuccinos/page/" + str(MenuSpider.page_number) + "/"
        #if menuSpider.page_number <= 21:
            #MenuSpider.page_number += 1
        #    yield response.follow(next_page, callback=self.parse)

    def parse_detail(self, response):
        ret = {}

        # name of secret drink
        name = response.css(".entry-title::text")


        recipe = ""
        time.sleep(1.14)
        for row in response.css("ul li::text"):
            recipe += row.get().replace('\n',' ') + '\n'
        ret["recipe"] = recipe.strip()
        yield ret

    def parse_name(self, name):
        if "|" in name:
            return name[:name.index("|")-1]
        elif ":" in name:
            return = name[name.index(":")+2:]
        else:
            return name
