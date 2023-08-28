import discord
import os
import requests
from datetime import datetime
from discord.ext import tasks
import sys
import json

token = sys.argv[1]
url = sys.argv[2]

intents = discord.Intents.default()
intents.presences = True

client = discord.Client(intents=intents)
session = requests.Session()

@client.event
async def on_ready():
    print(f'Logged in as {client.user}')
    update_activity.start()

@tasks.loop(minutes=5)
async def update_activity():
    with session.get(url) as response_API:
        data = json.loads(response_API.text)
    count = data.get('player_count_formatted', 'N/A')
    activity = discord.Activity(name=f'{count} online.', type=discord.ActivityType.watching)
    await client.change_presence(activity=activity)

try:
    client.run(token)
except discord.HTTPException as e:
    if e.status == 429:
        print("Too many requests")
    else:
        raise e
