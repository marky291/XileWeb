import discord
import os
import requests
from datetime import datetime
from discord.ext import tasks
import sys

token = sys.argv[1]
player_count = sys.argv[2]

intents = discord.Intents.default()
intents.presences = True  # This might be required if you want to update bot presence/activity

client = discord.Client(intents=intents)

@client.event
async def on_ready():
    print('We have logged in as {0.user}'.format(client))
    print(f'[{datetime.now()}] Starting the update_activity loop...')
    update_activity.start()  # Start the looped task

@tasks.loop(minutes=5)
async def update_activity():
    print(f'[{datetime.now()}] Player count: {player_count}')
    activity = discord.Activity(name=f'{player_count} online.', type=discord.ActivityType.watching)
    await client.change_presence(activity=activity)
try:
    print(f'[{datetime.now()}] Attempting to run the client...')
    client.run(token)
except discord.HTTPException as e:
    if e.status == 429:
        print("The Discord servers denied the connection for making too many requests")
        print("Get help from https://stackoverflow.com/questions/66724687/in-discord-py-how-to-solve-the-error-for-toomanyrequests")
    else:
        raise e
