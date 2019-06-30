import scrapy
import time

class MenuSpider(scrapy.Spider):
    name = "menu"
    page_number = 2

    start_urls = ["https://starbuckssecretmenu.net/category/secret-frappuccinos/"]

    def parse(self, response):
        for row in response.css(".td-block-span6"):
            time.sleep(1)
            detail_page = row.css("a::attr(href)").extract_first()
            url = response.urljoin(detail_page)
            yield scrapy.Request(url, callback = self.parse_detail)
            break

        #next_page = "https://starbuckssecretmenu.net/category/secret-frappuccinos/page/" + str(MenuSpider.page_number) + "/"
        #if menuSpider.page_number <= 21:
            #MenuSpider.page_number += 1
        #    yield response.follow(next_page, callback=self.parse)

    def parse_detail(self, response):
        ret = {}

        # recipe of secret drink
        recipe = ""
        time.sleep(5.24)
        for row in response.css("ul li::text"):
            recipe += row.get().replace('\n',' ') + '\n'
        recipe = recipe.strip()

        # if recipe exists then exit the method
        if not recipe:
            return

        # otherwise add info into the return dictionary
        # name of the secret drink
        name = response.css(".entry-title::text").extract_first()
        ret["name"] = self.parse_name(name)

        ret["recipe"] = recipe

        # category of secret menu
        category = response.css(".entry-crumb::text").getall()
        ret["category"] = category[1]

        # images of the secret drink
        images = []
        for image in response.css(".td-post-content img::attr(src)"):
            images.append(image.get())
        ret["images"] = images

        yield ret

    def parse_name(self, name):
        if "|" in name:
            return name[:name.index("|")-1]
        elif ":" in name:
            return name[name.index(":")+2:]
        else:
            return name
